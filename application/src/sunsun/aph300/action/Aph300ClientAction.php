<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\aph300\action;
use app\src\sunsun\common\helper\SnHelper;
use app\src\sunsun\aph300\logic\Aph300DeviceLogic;
use GatewayClient\Gateway;
use sunsun\aph300\req\Aph300DeviceUpdateReq;


/**
 * Class Aph300DeviceInfoAction
 * tcp 客户端通用操作
 * @package app\src\sunsun\aph300
 */
class Aph300ClientAction extends Aph300BaseAction
{
    public function sendMessage($did,$message){
        return $this->sendReqToClient($did,$message,new Aph300DeviceLogic());
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
        $req = new Aph300DeviceUpdateReq();
        $req->setUrl($url);
        $req->setSn(SnHelper::getSn());
        $req->setLen($len);
        return $this->sendReqToAph300Client($did,$req);
    }
}