<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:41
 */

namespace app\src\sunsun\aph300\model;


use think\Model;

class Aph300DeviceEvent extends Model
{
    protected $table = "sunsun_aph300_device_event";

    const PRO_STATUS_NOT_PROCESS = 0;
    const PRO_STATUS_PROCESSED = 1;
    const PRO_STATUS_FAILED = 2;

    /**
     * 事件类型 -  实时数据推送
     */
    const Event_Type_Data_PUSH = 1;

    /**
     * 事件类型 - 低温报警
     */
    const Event_Type_Low_Temp = 2;

    /**
     * 事件类型 - 高温报警
     */
    const Event_Type_High_Temp = 3;

    /**
     * 事件类型 - ph过低报警
     */
    const Event_Type_Low_Ph = 4;

    /**
     * 事件类型 - ph过高报警
     */
    const Event_Type_High_Ph = 5;
}