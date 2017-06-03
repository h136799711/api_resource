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
use app\src\email\action\EmailSendAction;
use app\src\extend\umeng\BoyePushApi;
use app\src\jobs\JobsHelper;
use app\src\sunsun\common\logic\UserDeviceLogic;
use app\src\sunsun\filterVat\logic\FilterVatDeviceEventLogic;
use app\src\sunsun\filterVat\model\FilterVatDeviceEvent;
use app\src\system\logic\ConfigLogic;
use app\src\user\logic\UcenterMemberLogic;
use think\Controller;

/**
 * Class FilterVatTask
 * 过滤桶定时任务
 * @package app\index\controller
 */
class FilterVatTask extends Controller
{
    private $now;
    public function index(){
        set_time_limit(0);
        $from = $this->request->get('from');
        $last_call_time = cache('last_call_time');
        $this->now = time();
        $interval = 24 * 60;
        //如果从crontab 或者 上次调用时间已经过去了24分钟
        if ($from == 'crontab' || $last_call_time + $interval < $this->now) {
            $this->sendEmailNotify();
            cache('last_call_time',time());
        }

        $str  = '上一次调用时间: '.date("Y-m-d H:i:s",$last_call_time);
        $last_call_time = cache('last_call_time');
        $str .= '<br/>下一次调用时间:'.date("Y-m-d H:i:s",$last_call_time + $interval);
        echo $str;
    }


    /**
     * 清除数据库
     * 1天调用一次
     */
    public function clear_db(){
        set_time_limit(0);
        $from = $this->request->get('from');
        $last_call_time = cache('last_call_time');
        $this->now = time();
        $interval = 24 * 3600;
        //如果从crontab 或者 上次调用时间已经过去了24分钟
        if ($from == 'crontab' || $last_call_time + $interval < $this->now) {
            JobsHelper::pushClearDeviceEventJob();
            cache('last_call_time',time());
        }

        $str  = '上一次调用时间: '.date("Y-m-d H:i:s",$last_call_time);
        $last_call_time = cache('last_call_time');
        $str .= '<br/>下一次调用时间:'.date("Y-m-d H:i:s",$last_call_time + $interval);
        echo $str;
    }

    /**
     * 发送邮件通知
     */
    private function sendEmailNotify()
    {
        //
        $page = ['curpage'=>1,'size'=>300];
        $userDeviceLogic = new UserDeviceLogic();
        $ucenterMemberLogic = new UcenterMemberLogic();
        $deviceEventLogic = new FilterVatDeviceEventLogic();
        $result = $deviceEventLogic->query(['pro_status'=>FilterVatDeviceEvent::PRO_STATUS_NOT_PROCESS],$page,"create_time asc");

        $list = $result['info']['list'];
        $allEntity = [];
        $now = time();
        //同一个用户同一个设备同一个故障不再推送
        $pushed = [];
        foreach ($list as $item){
            $id  = $item['id'];
            $did = $item['did'];
            $eventType = $item['event_type'];
//            $content = json_decode($item['event_info'],JSON_OBJECT_AS_ARRAY);
//            if(array_key_exists("code_desc",$content)){
//                $content = $content['code_desc'];
//            }
            $entity = ['id'=>$id,'update_time'=>$now,'pro_status'=>1];
            array_push($allEntity,$entity);

            $time   = $item['create_time'];

            //最多取10个，一台设备被10个人以上绑定的情况不处理，效率
            $result = $userDeviceLogic->query(['did'=>$did],['curpage'=>1,'size'=>20]);

            if(ValidateHelper::legalArrayResult($result)){
                $list = $result['info']['list'];

                foreach ($list as $deviceItem) {
                    $uid = $deviceItem['uid'];
                    $lang = $deviceItem['lang'];
                    $timezone = intval($deviceItem['timezone']);
                    $localTime   = gmdate('Y-m-d H:i:s', $timezone * 3600 + $time);
                    $nickname = $deviceItem['device_nickname'];
                    $key = 'u' . $uid . 'd' . $did . 't' . $item['event_type'];
                    if(array_key_exists($key,$pushed)){
                        continue;
                    }
                    $pushed[$key] = 1;

                    $result = $ucenterMemberLogic->getInfo(['id' => $uid]);
                    $member = $result['info'];
                    if (ValidateHelper::legalArrayResult($result)) {
                        $content = lang('filter_event_type_'.$eventType,'',$lang);
                        $email = $member['email'];
                        $data = [
                            'to_email' => $email,
                            'title' => lang('device_notify_title',['nickname'=>$nickname,'device_type'=>lang('filter',[],$lang)],$lang),
                            'content' =>  lang('device_notify_content',['time'=>$localTime,'content'=>$content],$lang)
                        ];
                        (new EmailSendAction())->send($data['to_email'],$data['title'],$data['content']);

                        if ($this->pushUMeng($data)) {
                            (new FilterVatDeviceEventLogic())->saveByID($id, $entity);
                        } else {
                            if ($item['trys_cnt'] > 3) {
                                $entity['pro_status'] = FilterVatDeviceEvent::PRO_STATUS_FAILED;
                                (new FilterVatDeviceEventLogic())->saveByID($id, $entity);
                            } else {
                                (new FilterVatDeviceEventLogic())->setInc(['id' => $id], 'trys_cnt', 1);
                            }
                        }
                    }
                }
            }
        }

        (new FilterVatDeviceEventLogic())->saveAll($allEntity);

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