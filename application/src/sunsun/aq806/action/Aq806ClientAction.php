<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\aq806\action;
use app\src\sunsun\common\helper\SnHelper;
use app\src\sunsun\aq806\logic\Aq806DeviceLogic;
use GatewayClient\Gateway;
use sunsun\aq806\req\Aq806DeviceUpdateReq;


/**
 * Class Aq806DeviceInfoAction
 * tcp 客户端通用操作
 * @package app\src\sunsun\aq806
 */
class Aq806ClientAction extends Aq806BaseAction
{
    public function sendMessage($did,$message){
        return $this->sendReqToClient($did,$message,new Aq806DeviceLogic());
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
        $req = new Aq806DeviceUpdateReq();
        $req->setUrl($url);
        $req->setSn(SnHelper::getSn());
        $req->setLen($len);
        return $this->sendReqToAq806Client($did,$req);
    }
}