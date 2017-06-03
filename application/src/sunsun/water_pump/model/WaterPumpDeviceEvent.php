<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:41
 */

namespace app\src\sunsun\water_pump\model;


use think\Model;

class WaterPumpDeviceEvent extends Model
{
    protected $table = "sunsun_water_pump_device_event";

    const PRO_STATUS_NOT_PROCESS = 0;
    const PRO_STATUS_PROCESSED = 1;
    const PRO_STATUS_FAILED = 2;

    /**
     * 事件类型 -  实时数据推送
     */
    const Event_Type_Data_PUSH = 1;

    /**
     * 事件类型 -  过流异常
     */
    const Event_Type_Overcurrent_Err = 2;
    /**
     * 事件类型 -  过电压异常
     */
    const Event_Type_Overvoltage_Err = 3;
    /**
     * 事件类型 -  欠电压异常
     */
    const Event_Type_Undervoltage_Err = 4;
    /**
     * 事件类型 -  堵转异常
     */
    const Event_Type_Stall_Err = 5;
    /**
     * 事件类型 -  空载异常
     */
    const Event_Type_No_load_Err = 6;

}