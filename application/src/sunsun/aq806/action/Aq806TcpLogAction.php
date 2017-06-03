<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\aq806\action;
use app\src\sunsun\aq806\logic\Aq806TcpLogLogic;


/**
 * Class Aq806DeviceInfoAction
 * tcp 客户端通用操作
 * @package app\src\sunsun\aq806
 */
class Aq806TcpLogAction extends Aq806BaseAction
{

    /**
     * 清除过期数据
     * @param $dataTimestamp
     * @return array
     */
    public function clearExpiredData($dataTimestamp){
        $result = (new Aq806TcpLogLogic())->delete(['create_time'=>['lt',$dataTimestamp]]);
        return $result;
    }
}