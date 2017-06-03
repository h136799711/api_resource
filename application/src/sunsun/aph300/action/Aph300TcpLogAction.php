<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\aph300\action;
use app\src\sunsun\aph300\logic\Aph300TcpLogLogic;


/**
 * Class Aph300DeviceInfoAction
 * tcp 客户端通用操作
 * @package app\src\sunsun\aph300
 */
class Aph300TcpLogAction extends Aph300BaseAction
{

    /**
     * 清除过期数据
     * @param $dataTimestamp
     * @return array
     */
    public function clearExpiredData($dataTimestamp){
        $result = (new Aph300TcpLogLogic())->delete(['create_time'=>['lt',$dataTimestamp]]);
        return $result;
    }
}