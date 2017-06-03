<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-20
 * Time: 18:15
 */

namespace app\sunsun\common;


class DeviceType
{
    /**
     * 过滤桶
     */
    const FilterVat = "filter_vat";

    /**
     * 炽鸟-摄像头
     */
    const Chiniao_Wifi_Camera = "ciniao_wifi_camera";


    /**
     * 是否合法的设备类型
     * @param $type
     * @return bool
     */
    public static function isRegisteredDeviceType($type){

        switch (strtolower($type)){
            case DeviceType::Chiniao_Wifi_Camera:
                return true;
                break;
            default:
                break;
        }
        return false;
    }
}