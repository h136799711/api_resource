<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-14
 * Time: 15:30
 */

namespace app\domain;
use app\src\sunsun\common\action\UserDeviceAction;
use sunsun\filter_vat\dal\FilterVatDeviceDal;


/**
 * Class SunsunFilterVatDomain
 * 过滤桶
 * @package app\domain
 */
class SunsunUserDeviceDomain extends BaseDomain
{
    public function __construct($algInstance, $data)
    {
        parent::__construct($algInstance, $data);
    }

    /**
     * 更新aph300设备的额外信息
     *
     */
    public function updateAph300Extra(){
        $id = $this->_post('id',0);
        $first_update_time = $this->_post('first_update_time','');
        $last_update_time = $this->_post('last_update_time','');
        $action = new UserDeviceAction();

        $id = intval($id);
        if($id <= 0){
            $this->apiReturnErr('设备id无效(-1)');
        }
        $result = $action->getInfo($id);
        $entity = $result['info'];
        if(empty($entity)){
            $this->apiReturnErr('设备id无效(-2)');
        }
        $extra = json_decode($entity['extra'],JSON_OBJECT_AS_ARRAY);

        if(!empty($first_update_time)){
            $extra['first_upd'] = $first_update_time;
        }

        if(!empty($last_update_time)){
            $extra['last_upd'] = $last_update_time;
        }
        $data = [];
        $data['extra'] = json_encode($extra);
        $result = (new UserDeviceAction())->userDeviceChange($id,$data);
        $this->exitWhenError($result,true);
    }

    public function query(){
        $uid = $this->_post('uid','');
        $result = (new UserDeviceAction())->query($uid);
        $this->exitWhenError($result,true);
    }

    /**
     *
     * 101: 增加设备温度值上下限
     * 102: 增加工作状态通知开关
     */
    public function change(){
        $this->checkVersion([102],"增加设备温度值上下限");
        $id = $this->_post('id','');
        $device_nickname = $this->_post('device_nickname','');
        $nickname_a = $this->_post('nickname_a','');
        $nickname_b = $this->_post('nickname_b','');
        $temp_min = $this->_post('temp_min','');
        $temp_max = $this->_post('temp_max','');
        $temp_alert = $this->_post('temp_alert','');
        $is_state_notify = $this->_post('is_state_notify','');
        $entity = array(
            'update_time'=>time()
        );
        if(!empty($device_nickname)){
            $entity['device_nickname'] = $device_nickname;
        }
        if(!empty($nickname_a)){
            $entity['nickname_a'] = $nickname_a;
        }
        if(!empty($nickname_b)){
            $entity['nickname_b'] = $nickname_b;
        }
        if(!empty($temp_min)){
            $entity['temp_min'] = $temp_min;
        }
        if(!empty($temp_max)){
            $entity['temp_max'] = $temp_max;
        }

        if(strlen($temp_alert) > 0){
            $entity['temp_alert'] = intval($temp_alert);
        }
        if(strlen($is_state_notify) > 0){
            $entity['is_state_notify'] = intval($is_state_notify);
        }

        $result = (new UserDeviceAction())->UserDevicechange($id,$entity);
        $this->exitWhenError($result,true);
    }

    /**
     * 101 增加时区
     * 102 增加语言 zh-cn | en
     */
    public function add(){
        $this->checkVersion(["100","102"],"102 增加语言参数lang");
        $did = $this->_post('did','');
        $uid = $this->_post('uid','');
        $device_nickname = $this->_post('device_nickname','森森设备');
        $device_type = $this->_post('device_type','');
        $timezone = $this->_post('timezone',8);//时区
        $lang = $this->_post('lang','zh-cn');//时区

        if(empty($device_type)){
            $device_type = substr($did,0,3);
        }

        $time = time();
        $entity = array(
            'did'=>$did,
            'device_nickname'=>$device_nickname,
            'uid'=>$uid,
            'device_type'=>$device_type,
            'create_time'=>$time,
            'update_time'=>$time,
            'timezone'=>$timezone,
            'lang'=>$lang,
            'extra'=>'',
        );

        $result = (new UserDeviceAction())->add($entity);
        $this->exitWhenError($result,true);
    }

    public function del(){
        $id = $this->_post('id','');
        $result = (new UserDeviceAction())->UserDeviceDel($id);
        $this->exitWhenError($result,true);
    }

}