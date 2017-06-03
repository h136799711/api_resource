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
use app\src\sunsun\aph300\action\Aph300DeviceEventAction;
use app\src\sunsun\aph300\action\Aph300PhHisAction;
use app\src\sunsun\aph300\action\Aph300TcpLogAction;
use app\src\sunsun\aph300\action\Aph300TempHisAction;
use app\src\sunsun\aph300\logic\Aph300DeviceEventLogic;
use app\src\sunsun\aph300\model\Aph300DeviceEvent;
use app\src\sunsun\common\logic\UserDeviceLogic;
use app\src\sunsun\heatingRod\logic\HeatingRodTempHisLogic;
use app\src\system\logic\ConfigLogic;
use sunsun\aph300\req\Aph300DeviceEventReq;
use think\Controller;

/**
 * Aph300设备事件任务处理
 * 设备事件处理任务
 * @package app\index\controller
 */
class Aph300DeviceEventTask extends Controller
{
    /**
     * aq806 日志清理
     */
    public function clear(){

        //4. tcp接口日志7天内保留
        $now = time();
        $dataTimestamp = $now - 7*24*3600;
        $result = (new Aph300TcpLogAction())->clearExpiredData($dataTimestamp);
        addLog("ClearDeviceEventData/fire",'清理aph300TCP通信日志',json_encode($result),"[定时任务]");
        $dataTimestamp = $now - 30*24*3600;
        $result = (new Aph300DeviceEventAction())->clearExpiredData($dataTimestamp);
        addLog("ClearDeviceEventData/fire",'清理aph300TCP 事件日志',json_encode($result),"[定时任务]");
    }

    public function index(){
        set_time_limit(0);
        $from = $this->request->get('from');
        if($from == "crontab"){
            $this->process();
            echo "process";
        }

    }

    /**
     * 处理aph300的事件
     */
    private function process()
    {
        //
        $page = ['curpage'=>1,'size'=>3000];
        $userDeviceLogic = new UserDeviceLogic();
        $deviceEventLogic = new Aph300DeviceEventLogic();
        $result = $deviceEventLogic->query(['pro_status'=>Aph300DeviceEvent::PRO_STATUS_NOT_PROCESS],$page,"create_time asc");

        $list = $result['info']['list'];
        $now = time();
        $pushed = [];
        foreach ($list as $item){

            $entity = ['update_time'=>$now,'pro_status'=>Aph300DeviceEvent::PRO_STATUS_PROCESSED];
            $id  = $item['id'];
            $did = $item['did'];
            $eventType = $item['event_type'];
            $content = json_decode($item['event_info'],JSON_OBJECT_AS_ARRAY);
            if($item['event_type'] == Aph300DeviceEvent::Event_Type_Data_PUSH){
                //记录ph值到数据库中
                $ret = (new Aph300PhHisAction())->add($did,$content,$item);
                if(ResultHelper::isSuccess($ret)){
                    (new Aph300DeviceEventLogic())->saveByID($id,$entity);
                }else{
                    $entity['pro_status'] = Aph300DeviceEvent::PRO_STATUS_FAILED;
                    (new Aph300DeviceEventLogic())->saveByID($id,$entity);
                }
                //记录t值到数据库中
                $ret = (new Aph300TempHisAction())->add($did,$content,$item);
                if(ResultHelper::isSuccess($ret)){
                    (new Aph300DeviceEventLogic())->saveByID($id,$entity);
                }else{
                    $entity['pro_status'] = Aph300DeviceEvent::PRO_STATUS_FAILED;
                    (new Aph300DeviceEventLogic())->saveByID($id,$entity);
                }
                continue;
            }


//            if(array_key_exists("code_desc",$content)){
//                $content = $content['code_desc'];
//            }


            $time   = $item['create_time'];
            $result = $userDeviceLogic->query(['did'=>$did],['curpage'=>1,'size'=>20]);

            if(!ValidateHelper::legalArrayResult($result) || empty($result['info']['list'])) {

                $entity['pro_status'] = Aph300DeviceEvent::PRO_STATUS_FAILED;
                (new Aph300DeviceEventLogic())->saveByID($id, $entity);
                continue;
            }
            $list = $result['info']['list'];

            foreach ($list as $deviceItem) {
                $uid = $deviceItem['uid'];
                $lang = $deviceItem['lang'];
                $timezone = intval($deviceItem['timezone']);
                $localTime   = gmdate('Y-m-d H:i:s', $timezone * 3600 + $time);
                $nickname = $deviceItem['device_nickname'];
                $key = 'u' . $uid . 'd' . $did . 't' . $item['event_type'];
                if (!array_key_exists($key, $pushed)) {
                    $pushed[$key] = 1;
                } else {
                    (new Aph300DeviceEventLogic())->saveByID($id, $entity);
                    continue;
                }

                $content = lang('aph300_event_type_'.$eventType,[],$lang);
                $data = [
                    'to_uid' => $uid,
                    'title' => lang('device_notify_title',['nickname'=>$nickname,'device_type'=>lang('aph300','',$lang)],$lang),
                    'content' =>  lang('device_notify_content',['time'=>$localTime,'content'=>$content],$lang)
                ];

                if ($this->pushUMeng($data)) {
                    (new Aph300DeviceEventLogic())->saveByID($id, $entity);
                } else {
                    if ($item['trys_cnt'] > 3) {
                        $entity['pro_status'] = Aph300DeviceEvent::PRO_STATUS_FAILED;
                        (new Aph300DeviceEventLogic())->saveByID($id, $entity);
                    } else {
                        (new Aph300DeviceEventLogic())->setInc(['id' => $id], 'trys_cnt', 1);
                    }
                }

            }
        }

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