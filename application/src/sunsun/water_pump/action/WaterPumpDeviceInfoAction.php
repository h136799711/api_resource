<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\water_pump\action;
use app\src\base\helper\ResultHelper;
use app\src\base\helper\ValidateHelper;
use app\src\sunsun\common\helper\SnHelper;
use app\src\sunsun\common\logic\UserDeviceLogic;
use app\src\sunsun\water_pump\logic\WaterPumpDeviceLogic;
use sunsun\water_pump\req\WaterPumpDeviceInfoReq;


/**
 * Class WaterPumpDeviceInfoAction
 * 获取设备信息
 * @package app\src\sunsun\water_pump
 */
class WaterPumpDeviceInfoAction extends WaterPumpBaseAction
{
    /**
     * 获取设备信息
     * @param $did
     * @param $uid
     * @return array
     */
    public function deviceInfo($did,$uid=0){
        if(intval($uid) < 0){
            return ResultHelper::error('该用户不存在');
        }
        if($uid ==  0){
            $deviceInfo = [];
        }else{
            $result = (new UserDeviceLogic())->getInfo(['uid'=>$uid,'did'=>$did]);
            if(!ValidateHelper::legalArrayResult($result)){
                return ResultHelper::error('该设备信息不存在');
            }
            $deviceInfo = $result['info'];
        }
        $result = (new WaterPumpDeviceLogic())->getInfo(['did'=>$did]);
        if(!ValidateHelper::legalArrayResult($result)){
            return ResultHelper::error('该设备信息不存在');
        }

        $result['info'] = array_merge($this->processDevice($result['info']),$deviceInfo);

        $deviceInfo = $result['info'];
        //设备信息3s以内如果更新过了，则不请求获取设备状态
        if($deviceInfo['update_time'] < time() - 3) {
            $req = new WaterPumpDeviceInfoReq();
            $req->setSn(SnHelper::getSn());
            $this->sendReqToWaterPumpClient($did, $req);
        }

        return $result;
    }

    private function processDevice($info){
        if(!is_array($info)){
            return $info;
        }
        //增加一个设备断开时间
        $update_time = strtotime($info['update_time']);
        $hb = $info['hb'];
        //3倍的心跳时间作为设备断开判断依据
        //相当于重试3次
        $info['logout_time'] = $update_time + 3*$hb;
        $info['is_disconnect'] = 0;
        if($info['logout_time'] < time()){
            $info['is_disconnect'] = 1;
        }
        //如果tcp通道id为空，则设备已断开
        if(empty($info['tcp_client_id'])){
            $info['is_disconnect'] = 2;
        }
        unset($info['logout_time']);
        return $info;
    }
}