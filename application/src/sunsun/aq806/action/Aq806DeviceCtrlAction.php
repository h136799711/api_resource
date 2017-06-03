<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\aq806\action;
use app\src\base\helper\ResultHelper;
use app\src\sunsun\common\helper\SnHelper;
use sunsun\aq806\req\Aq806CtrlDeviceReq;


/**
 * Class Aq806DeviceInfoAction
 * 获取设备信息
 * @package app\src\sunsun\aq806
 */
class Aq806DeviceCtrlAction extends Aq806BaseAction
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
        $req = new Aq806CtrlDeviceReq();
        $req->setSn(SnHelper::getSn());
        $req->setData($data);
        $result = $this->sendReqToAq806Client($did,$req);
        return $result;
    }
}