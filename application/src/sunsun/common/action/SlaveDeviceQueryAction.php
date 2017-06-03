<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-05-17
 * Time: 09:03
 */

namespace app\src\sunsun\common\action;


use app\src\base\action\BaseAction;
use app\src\sunsun\common\logic\SlaveDeviceLogic;

/**
 * Class SlaveDeviceBindAction
 * 从设备查询根据主设备id操作
 * @package app\src\sunsun\common\action
 */
class SlaveDeviceQueryAction extends BaseAction
{
    /**
     * 设备查询
     * @param integer $uid          用户id
     * @param string $master_did  主设备did
     * @return array
     */
    public function query($master_did){
        $map = [
            'master_did'=>$master_did
        ];

        return (new SlaveDeviceLogic())->queryNoPaging($map,'create_time desc');
    }
}