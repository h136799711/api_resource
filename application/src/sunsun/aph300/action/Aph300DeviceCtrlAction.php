<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\aph300\action;
use app\src\base\helper\ResultHelper;
use app\src\sunsun\common\helper\SnHelper;
use sunsun\aph300\req\Aph300CtrlDeviceReq;


/**
 * Class Aph300DeviceInfoAction
 * 获取设备信息
 * @package app\src\sunsun\aph300
 */
class Aph300DeviceCtrlAction extends Aph300BaseAction
{
    /**
     * 获取设备信息
     * @param $did
     * @param $data
     * @return array
     */
    public function ctrl($did,$data){
        if(empty($data)){
            return ResultHelper::error('操作失败(参数缺少)');
        }
        $req = new Aph300CtrlDeviceReq();
        $req->setSn(SnHelper::getSn());
        $req->setData($data);
        $result = $this->sendReqToAph300Client($did,$req);
        return $result;
    }
}