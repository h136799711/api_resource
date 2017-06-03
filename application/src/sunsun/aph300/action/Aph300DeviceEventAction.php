<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\aph300\action;

use app\src\sunsun\aph300\logic\Aph300DeviceEventLogic;
use app\src\sunsun\aph300\model\Aph300DeviceEvent;
use GatewayClient\Gateway;
use sunsun\aph300\req\Aph300DeviceEventReq;


/**
 * Class Aph300DeviceInfoAction
 * tcp 客户端通用操作
 * @package app\src\sunsun\aph300
 */
class Aph300DeviceEventAction extends Aph300BaseAction
{

    /**
     * 清除过期数据
     * @param $dataTimestamp
     * @return array
     */
    public function clearExpiredData($dataTimestamp){
        $map['create_time'] = ['lt',$dataTimestamp];
        $result = (new Aph300DeviceEventLogic())->delete($map);
        return $result;
    }

    public function add($did,$eventType,$ph,$t){
        $req = new Aph300DeviceEventReq();
        $req->setCode($eventType);
        $req->setPh($ph);
        $req->setT($t);
        $eventInfo = json_encode(['t'=>$req->getT(),'ph'=>$req->getPh(),'code_desc'=>$req->getCodeDesc()]);
        $data = [
            'did'=>$did,
            'event_type'=>$eventType,
            'event_info'=>$eventInfo,
            'create_time'=>time(),
            'update_time'=>time(),
            'pro_status'=>Aph300DeviceEvent::PRO_STATUS_NOT_PROCESS
        ];
        $result = (new Aph300DeviceEventLogic())->add($data);
        return $result;
    }

}