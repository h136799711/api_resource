<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-14
 * Time: 15:30
 */

namespace app\domain;
use app\src\base\enum\ErrorCode;
use app\src\base\helper\ResultHelper;
use app\src\base\helper\ValidateHelper;
use app\src\sunsun\water_pump\action\WaterPumpClientAction;
use app\src\sunsun\water_pump\action\WaterPumpDeviceCtrlAction;
use app\src\sunsun\water_pump\action\WaterPumpDeviceEventAction;
use app\src\sunsun\water_pump\action\WaterPumpDeviceInfoAction;
use app\src\sunsun\water_pump\action\VWaterPumpPhHisAction;
use app\src\sunsun\water_pump\action\VWaterPumpTempHisAction;
use app\src\sunsun\water_pump\logic\WaterPumpDeviceLogic;
use app\src\sunsun\common\action\DeviceGetVersionAction;
use app\src\sunsun\common\action\JudgeLegalCtrlPwdAction;
use app\src\sunsun\common\logic\DeviceVersionLogic;

/**
 * Class SunsunWaterPumpDomain
 * water_pump
 * @package app\domain
 */
class SunsunWaterPumpDomain extends BaseDomain
{
    public function __construct($algInstance, $data)
    {
        parent::__construct($algInstance, $data);

    }

    public function auth(){
        $ctrl_pwd = $this->_post('ctrl_pwd','');
        $did = $this->_post('did','');
        $result = (new JudgeLegalCtrlPwdAction())->judge(new WaterPumpDeviceLogic(),$did,$ctrl_pwd);
        if($result === false){
            $this->apiReturnErr(lang('invalid_ctrl_pwd'),ErrorCode::Api_Device_Ctrl_Pwd_Invalid);
        }
        $this->apiReturnSuc('success');
    }

    public function queryLatest(){
        $did = $this->_post('did','');
        $type = substr($did,0,3);
        $result = (new DeviceVersionLogic())->getLatest($type);
        if(!ValidateHelper::legalArrayResult($result)){
            $this->apiReturnErr('不支持该设备类型');
        }
        $this->returnResult($result);
    }

    public function updateFirmware(){
        $did = $this->_post('did','');
        $version = $this->_post('version','');

        $result = (new DeviceGetVersionAction())->version($did,$version);

        if(!ValidateHelper::legalArrayResult($result)){
            $this->apiReturnErr('不支持该设备类型');
        }
        $url = $result['info']['url'];
        $len = $result['info']['bytes'];

        $result = (new WaterPumpClientAction())->update($did,$url,$len);
        $this->returnResult($result);
    }

    public function sendMessage(){
        $did      = $this->_post('did','');
        $message = $this->_post('message','');
        $result   = (new WaterPumpClientAction())->sendMessage($did,json_decode($message,JSON_OBJECT_AS_ARRAY));
        $this->exitWhenError($result,true);
    }

    public function userDeviceInfo(){
        $did = $this->_post('did','');
        $uid = $this->_post('uid','');

        $result = (new WaterPumpDeviceInfoAction())->deviceInfo($did,$uid);

        $this->exitWhenError($result,true);
    }

    public function deviceInfo(){
        $did = $this->_post('did','');
        $uid = $this->_post('uid',0);

        $result = (new WaterPumpDeviceInfoAction())->deviceInfo($did,$uid);

        $this->exitWhenError($result,true);
    }

    public function clientCount(){
        $data = [
            'all_client_cnt'=>(new WaterPumpClientAction())->allClientCount()
        ];
        $result = ['status'=>true,'info'=>$data];
        $this->exitWhenError($result,true);
    }

    public function sessionInfo(){
        $did = $this->_post('did','');
        $data =  (new WaterPumpClientAction())->getSession($did);
        $result = ['status'=>true,'info'=>$data];
        $this->exitWhenError($result,true);
    }

    public function devicesCtrl(){

        $this->checkVersion(["100"],'参数改下划线模式');
        $debug = $this->_post('debug',0);
        $did = $this->_post('did','');
        $devLock = $this->_post('dev_lock','');
        $i_cyc = $this->_post('i_cyc','');//数据推送周期	设置单位分钟
        $gear = $this->_post('gear','');
        $cfg = $this->_post('cfg','');

        $entity = array();

        if(strlen($i_cyc) > 0){
            $entity['iCyc'] = intval($i_cyc);
        }
        if(empty($did)){
            $this->apiReturnErr('did参数缺失');
        }
        if(strlen($devLock) > 0){
            $entity['devLock'] = intval($devLock);
        }
        if(strlen($gear) > 0){
            $entity['gear'] = intval($gear);
        }
        if(strlen($cfg) > 0){
            $entity['cfg'] = intval($cfg);
        }
        if(count($entity) == 0){
            $this->apiReturnSuc('操作成功');
        }

        $result  = (new WaterPumpDeviceCtrlAction())->ctrl($did,$entity);
        if($result['status']) {
            //正式测试会注释该方法
            if($debug){
                $this->toDbEntity($did,$entity);
            }
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr($result['info']);
        }
    }

    private function toDbEntity($did,$data){
        $dbEntity = [];

        array_key_exists("devLock",$data)  && $dbEntity['dev_lock'] = $data['devLock'];
        array_key_exists("cfg",$data)  && $dbEntity['cfg'] = $data['cfg'];
        array_key_exists("iCyc",$data)  && $dbEntity['i_cyc'] = $data['iCyc'];
        array_key_exists("gear",$data)  && $dbEntity['gear'] = $data['gear'];

        (new WaterPumpDeviceLogic())->save(['did'=>$did],$dbEntity);
        return $dbEntity;
    }

    public function deviceEvent(){
        $did = $this->_post('did','');
        $code = $this->_post('code','');
        $ph = $this->_post('ph','');
        $t = $this->_post('t','');

        $action = new WaterPumpDeviceEventAction();
        $result = $action->add($did,$code,$ph,$t);

        $this->returnResult($result);
    }

}