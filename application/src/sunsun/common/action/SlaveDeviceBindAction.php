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
 * 绑定设备操作
 * @package app\src\sunsun\common\action
 */
class SlaveDeviceBindAction extends BaseAction
{
    /**
     * 设备绑定
     * @param string $master_did  主设备did
     * @param string $slave_type  从设备类型
     * @param string $slave_did   从设备did
     * @param string $slave_pwd   从设备密码
     * @param string $slave_name  从设备名称
     * @return array
     */
    public function bind($master_did,$slave_type,$slave_did,$slave_pwd='',$slave_name='设备名称'){
        $entity = [
            'master_did'=>$master_did,
            'slave_did'=>$slave_did,
            'slave_device_type'=>$slave_type,
            'slave_pwd'=>$slave_pwd,
            'slave_name'=>$slave_name
        ];

        return (new SlaveDeviceLogic())->add($entity);
    }
}