<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\aq806\action;

use app\src\sunsun\aq806\logic\Aq806DeviceEventLogic;
use app\src\sunsun\aq806\model\Aq806DeviceEvent;
use GatewayClient\Gateway;
use sunsun\aq806\req\Aq806DeviceEventReq;


/**
 * Class Aq806DeviceInfoAction
 * tcp 客户端通用操作
 * @package app\src\sunsun\aq806
 */
class Aq806DeviceEventAction extends Aq806BaseAction
{

    /**
     * 清除过期数据
     * @param $dataTimestamp
     * @return array
     */
    public function clearExpiredData($dataTimestamp){
        $map['create_time'] = ['lt',$dataTimestamp];
        $result = (new Aq806DeviceEventLogic())->delete($map);
        return $result;
    }

    public function add($did,$eventType,$ph,$t,$dyn){
        $req = new Aq806DeviceEventReq();
        $req->setCode($eventType);
        $req->setPh($ph);
        $req->setT($t);
        $req->setDyn($dyn);
        $eventInfo = json_encode(['dyn'=>$req->getDyn(),'t'=>$req->getT(),'ph'=>$req->getPh(),'code_desc'=>'['.$req->getCodeDesc().'] '.$req->getDynDesc()]);
        $data = [
            'did'=>$did,
            'event_type'=>$eventType,
            'event_info'=>$eventInfo,
            'create_time'=>time(),
            'update_time'=>time(),
            'pro_status'=>Aq806DeviceEvent::PRO_STATUS_NOT_PROCESS
        ];
        $result = (new Aq806DeviceEventLogic())->add($data);
        return $result;
    }

}