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
use app\src\sunsun\aph300\action\Aph300ClientAction;
use app\src\sunsun\aph300\action\Aph300DeviceCtrlAction;
use app\src\sunsun\aph300\action\Aph300DeviceEventAction;
use app\src\sunsun\aph300\action\Aph300DeviceInfoAction;
use app\src\sunsun\aph300\action\VAph300PhHisAction;
use app\src\sunsun\aph300\action\VAph300TempHisAction;
use app\src\sunsun\aph300\logic\Aph300DeviceLogic;
use app\src\sunsun\common\action\DeviceGetVersionAction;
use app\src\sunsun\common\action\JudgeLegalCtrlPwdAction;
use app\src\sunsun\common\logic\DeviceVersionLogic;

/**
 * Class SunsunAph300Domain
 * aph300
 * @package app\domain
 */
class SunsunAph300Domain extends BaseDomain
{
    public function __construct($algInstance, $data)
    {
        parent::__construct($algInstance, $data);

    }

    public function auth(){
        $ctrl_pwd = $this->_post('ctrl_pwd','');
        $did = $this->_post('did','');
        $result = (new JudgeLegalCtrlPwdAction())->judge(new Aph300DeviceLogic(),$did,$ctrl_pwd);
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

        $result = (new Aph300ClientAction())->update($did,$url,$len);
        $this->returnResult($result);
    }

    public function sendMessage(){
        $did      = $this->_post('did','');
        $message = $this->_post('message','');
        $result   = (new Aph300ClientAction())->sendMessage($did,json_decode($message,JSON_OBJECT_AS_ARRAY));
        $this->exitWhenError($result,true);
    }

    public function userDeviceInfo(){
        $did = $this->_post('did','');
        $uid = $this->_post('uid','');

        $result = (new Aph300DeviceInfoAction())->deviceInfo($did,$uid);

        $this->exitWhenError($result,true);
    }

    public function deviceInfo(){
        $did = $this->_post('did','');
        $uid = $this->_post('uid',0);

        $result = (new Aph300DeviceInfoAction())->deviceInfo($did,$uid);

        $this->exitWhenError($result,true);
    }

    public function clientCount(){
        $data = [
            'all_client_cnt'=>(new Aph300ClientAction())->allClientCount()
        ];
        $result = ['status'=>true,'info'=>$data];
        $this->exitWhenError($result,true);
    }

    public function sessionInfo(){
        $did = $this->_post('did','');
        $data =  (new Aph300ClientAction())->getSession($did);
        $result = ['status'=>true,'info'=>$data];
        $this->exitWhenError($result,true);
    }

    public function devicesCtrl(){

        $this->checkVersion(["100"],'参数改下划线模式');
        $debug = $this->_post('debug',0);

        $did = $this->_post('did','');
        $devLock = $this->_post('dev_lock','');
        $th = $this->_post('th','');
        $tl = $this->_post('tl','');
        $push_cfg = $this->_post('push_cfg','');
        $d_cyc = $this->_post('d_cyc','');//数据推送周期	设置单位分钟
        $phh = $this->_post('phh','');
        $phl = $this->_post('phl','');
        $ph_cmd = $this->_post('ph_cmd','');
        $ph_dly = $this->_post('ph_dly','');

        $entity = array();

        if(strlen($d_cyc) > 0){
            $entity['dCyc'] = intval($d_cyc);
        }
        if(strlen($push_cfg) > 0){
            $entity['pushCfg'] = intval($push_cfg);
        }
        if(strlen($th) > 0){
            $entity['th'] = intval($th);
        }
        if(strlen($tl) > 0){
            $entity['tl'] = intval($tl);
        }
        if(empty($did)){
            $this->apiReturnErr('did参数缺失');
        }
        if(strlen($devLock) > 0){
            $entity['devLock'] = intval($devLock);
        }
        if(strlen($ph_dly) > 0){
            $entity['ph_dly'] = intval($ph_dly);
        }
        if(strlen($ph_cmd) > 0){
            $entity['ph_cmd'] = intval($ph_cmd);
        }
        if(strlen($phh) > 0){
            $entity['phh'] = intval($phh);
        }
        if(strlen($phl) > 0){
            $entity['phl'] = intval($phl);
        }
        if(count($entity) == 0){
            $this->apiReturnSuc('操作成功');
        }

        $result  = (new Aph300DeviceCtrlAction())->ctrl($did,$entity);
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

        array_key_exists("th",$data)  && $dbEntity['th'] = $data['th'];
        array_key_exists("tl",$data)  && $dbEntity['tl'] = $data['tl'];
        array_key_exists("devLock",$data)  && $dbEntity['dev_lock'] = $data['devLock'];
        array_key_exists("pushCfg",$data)  && $dbEntity['push_cfg'] = $data['pushCfg'];
        array_key_exists("dCyc",$data)  && $dbEntity['d_cyc'] = $data['dCyc'];
        array_key_exists("ph_dly",$data)  && $dbEntity['ph_dly'] = $data['ph_dly'];
        array_key_exists("ph_cmd",$data)  && $dbEntity['ph_cmd'] = $data['ph_cmd'];
        array_key_exists("phh",$data)  && $dbEntity['phh'] = $data['phh'];
        array_key_exists("phl",$data)  && $dbEntity['phl'] = $data['phl'];

        (new Aph300DeviceLogic())->save(['did'=>$did],$dbEntity);
        return $dbEntity;
    }

    public function deviceEvent(){
        $did = $this->_post('did','');
        $code = $this->_post('code','');
        $ph = $this->_post('ph','');
        $t = $this->_post('t','');

        $action = new Aph300DeviceEventAction();
        $result = $action->add($did,$code,$ph,$t);

        $this->returnResult($result);
    }

    /**
     * 历史ph 值 数据
     */
    public function queryHistoryPh(){

        $date_type = $this->_post('date_type','');
        $did  = $this->_post('did','');

        if(!empty($date_type)){
            $result  = ResultHelper::error('error type');
            switch (intval($date_type)){
                case 1:
                    //24小时
                    $result = (new VAph300PhHisAction())->oneDay($did);
                    break;
                case 2:
                    //7天
                    $result = (new VAph300PhHisAction())->lastNDay($did,7);
                    break;
                case 3:
                    //30天
                    $result = (new VAph300PhHisAction())->lastNDay($did,30);
                    break;
                default:
                    $this->apiReturnErr('type参数错误');
                    break;
            }
            $this->returnResult($result);
        }
        $this->apiReturnErr('type参数错误');
    }

    /**
     * 历史温度 值 数据
     */
    public function queryHistoryTemp(){

        $date_type = $this->_post('date_type','');
        $did  = $this->_post('did','');

        if(!empty($date_type)){
            $result  = ResultHelper::error('error type');
            switch (intval($date_type)){
                case 1:
                    //24小时
                    $result = (new VAph300TempHisAction())->oneDay($did);
                    break;
                case 2:
                    //7天
                    $result = (new VAph300TempHisAction())->lastNDay($did,7);
                    break;
                case 3:
                    //30天
                    $result = (new VAph300TempHisAction())->lastNDay($did,30);
                    break;
                default:
                    $this->apiReturnErr('type参数错误');
                    break;
            }
            $this->returnResult($result);
        }
        $this->apiReturnErr('type参数错误');
    }

}