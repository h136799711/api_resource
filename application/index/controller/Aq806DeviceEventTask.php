<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-11
 * Time: 14:54
 */

namespace app\index\controller;

use app\src\base\helper\ResultHelper;
use app\src\base\helper\ValidateHelper;
use app\src\extend\umeng\BoyePushApi;
use app\src\sunsun\aq806\action\Aq806DeviceEventAction;
use app\src\sunsun\aq806\action\Aq806PhHisAction;
use app\src\sunsun\aq806\action\Aq806TcpLogAction;
use app\src\sunsun\aq806\action\Aq806TempHisAction;
use app\src\sunsun\aq806\logic\Aq806DeviceEventLogic;
use app\src\sunsun\aq806\model\Aq806DeviceEvent;
use app\src\sunsun\common\logic\UserDeviceLogic;
use app\src\sunsun\heatingRod\logic\HeatingRodTempHisLogic;
use app\src\system\logic\ConfigLogic;
use sunsun\aq806\req\Aq806DeviceEventReq;
use think\Controller;

/**
 * Aq806设备事件任务处理
 * 设备事件处理任务
 * @package app\index\controller
 */
class Aq806DeviceEventTask extends Controller
{
    public function index(){
        set_time_limit(0);
        $from = $this->request->get('from');
        if($from == "crontab"){
            $this->process();
            echo "process";
        }

    }

    /**
     * aq806 日志清理
     */
    public function clear(){

        //4. tcp接口日志7天内保留
        $now = time();
        $dataTimestamp = $now - 7*24*3600;
        $result = (new Aq806TcpLogAction())->clearExpiredData($dataTimestamp);
        addLog("ClearDeviceEventData/fire",'清理aq806TCP通信日志',json_encode($result),"[定时任务]");
        $dataTimestamp = $now - 30*24*3600;
        $result = (new Aq806DeviceEventAction())->clearExpiredData($dataTimestamp);
        addLog("ClearDeviceEventData/fire",'清理aq806TCP 事件日志',json_encode($result),"[定时任务]");
    }

    /**
     * 处理aq806的事件
     */
    private function process()
    {
        //
        $page = ['curpage'=>1,'size'=>3000];
        $userDeviceLogic = new UserDeviceLogic();
        $deviceEventLogic = new Aq806DeviceEventLogic();
        $result = $deviceEventLogic->query(['pro_status'=>Aq806DeviceEvent::PRO_STATUS_NOT_PROCESS],$page,"create_time asc");

        $list = $result['info']['list'];
        $now = time();
        $pushed = [];
        foreach ($list as $item){

            $entity = ['update_time'=>$now,'pro_status'=>Aq806DeviceEvent::PRO_STATUS_PROCESSED];
            $id  = $item['id'];
            $did = $item['did'];
            $eventType = $item['event_type'];
            $content = json_decode($item['event_info'],JSON_OBJECT_AS_ARRAY);
            if($item['event_type'] == Aq806DeviceEvent::Event_Type_PH_PUSH){
                //记录ph值到数据库中
                $ret = (new Aq806PhHisAction())->add($did,$content,$item);
                if(ResultHelper::isSuccess($ret)){
                    (new Aq806DeviceEventLogic())->saveByID($id,$entity);
                }else{
                    $entity['pro_status'] = Aq806DeviceEvent::PRO_STATUS_FAILED;
                    (new Aq806DeviceEventLogic())->saveByID($id,$entity);
                }
                //记录t值到数据库中
                $ret = (new Aq806TempHisAction())->add($did,$content,$item);
                if(ResultHelper::isSuccess($ret)){
                    (new Aq806DeviceEventLogic())->saveByID($id,$entity);
                }else{
                    $entity['pro_status'] = Aq806DeviceEvent::PRO_STATUS_FAILED;
                    (new Aq806DeviceEventLogic())->saveByID($id,$entity);
                }
                continue;
            }
//            {"dyn":"33","t":"","ph":"","code_desc":"[\u52a8\u6001\u63d0\u793a] \u51b2\u6d6a\u6cf5\u5173\u95ed\u4e86;\u7167\u660e\u706f\u6253\u5f00\u4e86;"}


            $time   = $item['create_time'];
            $result = $userDeviceLogic->query(['did'=>$did],['curpage'=>1,'size'=>20]);

            if(!ValidateHelper::legalArrayResult($result) || empty($result['info']['list'])) {

                $entity['pro_status'] = Aq806DeviceEvent::PRO_STATUS_FAILED;
                (new Aq806DeviceEventLogic())->saveByID($id, $entity);
                continue;
            }
            $list = $result['info']['list'];
            $dyn = -1;
            if(array_key_exists("dyn",$content)){
                $dyn = $content['dyn'];
            }
            foreach ($list as $deviceItem) {
                $uid = $deviceItem['uid'];
                $lang = $deviceItem['lang'];
                $timezone = intval($deviceItem['timezone']);
                $eventTypeDesc = lang('aq806_event_type_'.$eventType,[],$lang);
                $dyn_desc = $this->getDynDesc($dyn,$lang);
                // 拼接上动态提示内容
                $content = '['.$eventTypeDesc.']'.$dyn_desc;

                $localTime   = gmdate('Y-m-d H:i:s', $timezone * 3600 + $time);
                $nickname = $deviceItem['device_nickname'];
                $key = 'u' . $uid . 'd' . $did . 't' . $item['event_type'];
                if (!array_key_exists($key, $pushed)) {
                    $pushed[$key] = 1;
                } else {
                    (new Aq806DeviceEventLogic())->saveByID($id, $entity);
                    continue;
                }

                $data = [
                    'to_uid' => $uid,
                    'title' => lang('device_notify_title',['nickname'=>$nickname,'device_type'=>lang('aq806','',$lang)],$lang),
                    'content' =>  lang('device_notify_content',['time'=>$localTime,'content'=>$content],$lang)
                ];

                if ($this->pushUMeng($data)) {
                    (new Aq806DeviceEventLogic())->saveByID($id, $entity);
                } else {
                    if ($item['trys_cnt'] > 3) {
                        $entity['pro_status'] = Aq806DeviceEvent::PRO_STATUS_FAILED;
                        (new Aq806DeviceEventLogic())->saveByID($id, $entity);
                    } else {
                        (new Aq806DeviceEventLogic())->setInc(['id' => $id], 'trys_cnt', 1);
                    }
                }

            }
        }

    }

    private function getDynDesc($dyn,$lang){
        $dyn = intval($dyn);
        if (empty($dyn) || $dyn < 0) return "";
        //照明灯动态
        $light_on = 1;
        $light_off = 2;
        //杀菌灯动态
        $Germicidal_on = 4;
        $Germicidal_off = 8;
        //冲浪泵动态
        $Surfing_pump_on = 16;
        $Surfing_pump_off = 32;
        //模式动态
        $Surfing_pump_auto = 64;
        $Surfing_pump_manual = 128;
        $desc = "";

        if (($dyn & $Surfing_pump_auto) == $Surfing_pump_auto) {
            $desc .= lang('aq806_mode_auto_on',[],$lang);
        }
        if (($dyn & $Surfing_pump_manual) == $Surfing_pump_manual) {
            $desc .= lang('aq806_mode_auto_ff',[],$lang);
        }

        if (($dyn & $Surfing_pump_on) == $Surfing_pump_on) {
            $desc .= lang('aq806_mode_surfing_pump_on',[],$lang);
        }
        if (($dyn & $Surfing_pump_off) == $Surfing_pump_off) {
            $desc .= lang('aq806_mode_surfing_pump_off',[],$lang);
        }

        if (($dyn & $Germicidal_on) == $Germicidal_on) {
            $desc .= lang('aq806_mode_lamp_on',[],$lang);
        }
        if (($dyn & $Germicidal_off) == $Germicidal_off) {
            $desc .= lang('aq806_mode_lamp_off',[],$lang);
        }

        if (($dyn & $light_on) == $light_on) {
            $desc .= lang('aq806_mode_light_on',[],$lang);
        }
        if (($dyn & $light_off) == $light_off) {
            $desc .= lang('aq806_mode_light_off',[],$lang);
        }

        return $desc;
    }


    public function pushUMeng($data){
        $to_uid = $data['to_uid'];
        $content = $data['content'];
        $title   =  $data['title'];
        $msg_type = "00Q002001";

        $pushApi = new BoyePushApi();
        $after_open  = [
            'type'  => 'go_activity',
            'param' => $msg_type,
            'extra' => [],//'uid'=>$to_uid]
        ];
        $body = [
            // 'alert'  =>$summary,
            'alert'  =>$content,
            'ticker' =>$title,
            'title'  =>$title,
            'text'   =>$content
        ];


        $result = (new ConfigLogic())->queryNoPaging(['group'=>11]);

        //TODO: 增加缓存
        if(ValidateHelper::legalArrayResult($result)){
            $list = $result['info'];
            foreach ($list  as $vo){
//                if(strpos($vo['name'],'umeng') !== 0){
//                    continue;
//                }
                $value = $vo['value'];
                $value = $this->_parse($vo['type'],$value);
                if(is_array($value)){
                    $pushApi->setConfig($value);
                }

                $result = $pushApi->send($to_uid,$body,$after_open);
                if(!$result['status']) {
                    addLog('push_task','to_uid'.$to_uid.'config='.json_encode($value),$result['info'],'app_push推送');
                }

            }
        }

        return ResultHelper::success('success');
    }

    private function _parse($type, $value) {
        switch ($type) {
            case 3 :
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val,2);
                        $value[$k] = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }
        return $value;
    }
}