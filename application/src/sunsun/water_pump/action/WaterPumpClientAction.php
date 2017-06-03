<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\water_pump\action;
use app\src\sunsun\common\helper\SnHelper;
use app\src\sunsun\water_pump\logic\WaterPumpDeviceLogic;
use GatewayClient\Gateway;
use sunsun\water_pump\req\WaterPumpDeviceUpdateReq;


/**
 * Class WaterPumpDeviceInfoAction
 * tcp 客户端通用操作
 * @package app\src\sunsun\water_pump
 */
class WaterPumpClientAction extends WaterPumpBaseAction
{
    public function sendMessage($did,$message){
        return $this->sendReqToClient($did,$message,new WaterPumpDeviceLogic());
    }

    /**
     * 获取设备信息
     * @return int
     */
    public function allClientCount(){
        return Gateway::getAllClientCount();
    }

    public function getSession($did){
        $client_id = Gateway::getClientIdByUid($did);
        if(count($client_id) > 0){
            return Gateway::getSession($client_id[0]);
        }

        return null;
    }


    public function update($did,$url,$len=0){
        $req = new WaterPumpDeviceUpdateReq();
        $req->setUrl($url);
        $req->setSn(SnHelper::getSn());
        $req->setLen($len);
        return $this->sendReqToWaterPumpClient($did,$req);
    }
}