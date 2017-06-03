<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-05-17
 * Time: 09:19
 */

namespace app\domain;


use app\src\sunsun\common\action\SlaveDeviceBindAction;
use app\src\sunsun\common\action\SlaveDeviceQueryAction;

class SunsunSlaveDeviceDomain extends BaseDomain
{
    /**
     * 从设备绑定
     */
    public function bind(){
        $slave_pwd = $this->_post('slave_pwd','');
        $slave_name = $this->_post('slave_name','');
        $slave_device_type = $this->_post('slave_device_type','缺少从设备类型');
        $master_did = $this->_post('master_did','','缺少主设备did');
        $slave_did = $this->_post('slave_did','','缺少从设备did');

        $action = new SlaveDeviceBindAction();

        $result = $action->bind($master_did,$slave_device_type,$slave_did,$slave_pwd,$slave_name);

        $this->returnResult($result);
    }

    /**
     * 从设备查询
     */
    public function query(){
        $master_did = $this->_post('master_did','','缺少主设备did');

        $action = new SlaveDeviceQueryAction();

        $result = $action->query($master_did);

        $this->returnResult($result);
    }
}