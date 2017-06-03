<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\water_pump\action;
use app\src\sunsun\water_pump\logic\WaterPumpTcpLogLogic;


/**
 * Class WaterPumpDeviceInfoAction
 * tcp 客户端通用操作
 * @package app\src\sunsun\water_pump
 */
class WaterPumpTcpLogAction extends WaterPumpBaseAction
{

    /**
     * 清除过期数据
     * @param $dataTimestamp
     * @return array
     */
    public function clearExpiredData($dataTimestamp){
        $result = (new WaterPumpTcpLogLogic())->delete(['create_time'=>['lt',$dataTimestamp]]);
        return $result;
    }
}