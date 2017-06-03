<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:41
 */

namespace app\src\sunsun\aq806\model;


use think\Model;

class Aq806DeviceEvent extends Model
{
    protected $table = "sunsun_aq806_device_event";

    const PRO_STATUS_NOT_PROCESS = 0;
    const PRO_STATUS_PROCESSED = 1;
    const PRO_STATUS_FAILED = 2;

    /**
     * 事件类型- ph值推送
     */
    const Event_Type_PH_PUSH = 10;

    /**
     * 事件类型 - 动态提示
     */
    const Event_Type_Auto_Tip = 11;
}