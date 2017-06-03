<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\water_pump\action;

use app\src\sunsun\water_pump\logic\WaterPumpDeviceEventLogic;
use app\src\sunsun\water_pump\model\WaterPumpDeviceEvent;
use GatewayClient\Gateway;
use sunsun\water_pump\req\WaterPumpDeviceEventReq;


/**
 * Class WaterPumpDeviceInfoAction
 * tcp 客户端通用操作
 * @package app\src\sunsun\water_pump
 */
class WaterPumpDeviceEventAction extends WaterPumpBaseAction
{

    /**
     * 清除过期数据
     * @param $dataTimestamp
     * @return array
     */
    public function clearExpiredData($dataTimestamp){
        $map['create_time'] = ['lt',$dataTimestamp];
        $result = (new WaterPumpDeviceEventLogic())->delete($map);
        return $result;
    }

    public function add($did,$eventType,$ph,$t){
        $req = new WaterPumpDeviceEventReq();
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
            'pro_status'=>WaterPumpDeviceEvent::PRO_STATUS_NOT_PROCESS
        ];
        $result = (new WaterPumpDeviceEventLogic())->add($data);
        return $result;
    }

}