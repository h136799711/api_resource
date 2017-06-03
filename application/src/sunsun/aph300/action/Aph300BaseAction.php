<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:38
 */

namespace app\src\sunsun\aph300\action;

use app\src\base\helper\ResultHelper;
use app\src\base\logic\BaseLogic;
use app\src\sunsun\common\helper\SunsunEncodeHelper;
use app\src\sunsun\aph300\logic\Aph300DeviceLogic;
use GatewayClient\Gateway;
use sunsun\po\BaseReqPo;
use think\Exception;

require_once __DIR__ . '/../../../../../vendor/autoload.php';

//设置register服务地址
//aph300 register服务 端口1240
Gateway::$registerAddress = "101.37.37.167:1240";

class Aph300BaseAction
{

    /**
     * 发送数据到 过滤桶设备
     * @param $did
     * @param BaseReqPo $req
     * @return array
     */
    protected function sendReqToAph300Client($did,BaseReqPo $req){
        return $this->sendReqToClient($did,$req->toDataArray(),new Aph300DeviceLogic());
    }

    protected function sendReqToClient($did,$data,BaseLogic $logic){
        if(empty($data)){
            return ResultHelper::error('不能发送空数据');
        }

        addLog("Aph300BaseAction_sendReqToClient",$data,"发送的数据","发送给设备的未加密数据");
        $ret = SunsunEncodeHelper::encode($did,$data,$logic);
        if($ret['status']){
            $ret = $this->sendToClient($did,$ret['info']);
        }
        return $ret;
    }

//    protected function sendReqToClient($did,BaseReqPo $req,BaseLogic $logic){
//        $msg = $req->toDataArray();
//        if(empty($msg)){
//            return ResultHelper::error('不能发送空数据');
//        }
//        $ret = SunsunEncodeHelper::encode($did,$msg,$logic);
//        if($ret['status']){
//            $ret = $this->sendToClient($did,$ret['info']);
//        }
//        return $ret;
//    }
    protected function sendToClient($did,$msg){
        if(empty($msg)){
            return ResultHelper::error('不能发送空数据');
        }
        try{
            if(!empty($tcp_client_id)){
                Gateway::sendToClient($tcp_client_id,$msg);
            }else{
                Gateway::sendToUid($did,$msg);
            }
        }catch (Exception $ex){
            return ResultHelper::error('通讯异常，请重试');
        }
        return ResultHelper::success('发送成功');
    }
}