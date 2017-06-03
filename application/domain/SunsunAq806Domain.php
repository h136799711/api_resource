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
use app\src\sunsun\aq806\action\Aq806ClientAction;
use app\src\sunsun\aq806\action\Aq806DeviceCtrlAction;
use app\src\sunsun\aq806\action\Aq806DeviceEventAction;
use app\src\sunsun\aq806\action\Aq806DeviceInfoAction;
use app\src\sunsun\aq806\action\VAq806PhHisAction;
use app\src\sunsun\aq806\action\VAq806TempHisAction;
use app\src\sunsun\aq806\logic\Aq806DeviceLogic;
use app\src\sunsun\common\action\DeviceGetVersionAction;
use app\src\sunsun\common\action\JudgeLegalCtrlPwdAction;
use app\src\sunsun\common\logic\DeviceVersionLogic;

/**
 * Class SunsunAq806Domain
 * 过滤桶
 * @package app\domain
 */
class SunsunAq806Domain extends BaseDomain
{
    public function __construct($algInstance, $data)
    {
        parent::__construct($algInstance, $data);

    }

    public function auth(){
        $ctrl_pwd = $this->_post('ctrl_pwd','');
        $did = $this->_post('did','');
        $result = (new JudgeLegalCtrlPwdAction())->judge(new Aq806DeviceLogic(),$did,$ctrl_pwd);
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

        $result = (new Aq806ClientAction())->update($did,$url,$len);
        $this->returnResult($result);
    }

    public function sendMessage(){
        $did      = $this->_post('did','');
        $message = $this->_post('message','');
        $result   = (new Aq806ClientAction())->sendMessage($did,json_decode($message,JSON_OBJECT_AS_ARRAY));
        $this->exitWhenError($result,true);
    }

    public function userDeviceInfo(){
        $did = $this->_post('did','');
        $uid = $this->_post('uid','');

        $result = (new Aq806DeviceInfoAction())->deviceInfo($did,$uid);

        $this->exitWhenError($result,true);
    }

    public function deviceInfo(){
        $did = $this->_post('did','');
        $uid = $this->_post('uid',0);

        $result = (new Aq806DeviceInfoAction())->deviceInfo($did,$uid);

        $this->exitWhenError($result,true);
    }

    public function clientCount(){
        $data = [
            'all_client_cnt'=>(new Aq806ClientAction())->allClientCount()
        ];
        $result = ['status'=>true,'info'=>$data];
        $this->exitWhenError($result,true);
    }

    public function sessionInfo(){
        $did = $this->_post('did','');
        $data =  (new Aq806ClientAction())->getSession($did);
        $result = ['status'=>true,'info'=>$data];
        $this->exitWhenError($result,true);
    }

    public function devicesCtrl(){

        $this->checkVersion(["100"],'参数改下划线模式');
        $debug = $this->_post('debug',0);

        $did = $this->_post('did','');
        $devLock = $this->_post('dev_lock','');
        $tmL = $this->_post('tm_l','');
        $mode = $this->_post('mode','');
        $out_uvc = $this->_post('out_uvc','');
        $out_sp = $this->_post('out_sp','');
        $out_l = $this->_post('out_l','');
        $tMax = $this->_post('tMax','');
        $th = $this->_post('th','');
        $tl = $this->_post('tl','');
        $l_per = $this->_post('l_per','');
        $uvc_per = $this->_post('uvc_per','');
        $sp_per = $this->_post('sp_per','');
        $push_cfg = $this->_post('push_cfg','');
        $d_cyc = $this->_post('d_cyc','');//数据推送周期	设置单位分钟
        $uv_wh = $this->_post('uv_wh','');//杀菌灯已使用时间	设置单位小时
        $p_wh = $this->_post('p_wh','');//冲浪泵已使用时间	设置单位小时
        $l_wh = $this->_post('l_wh','');//照明灯已使用时间	设置单位小时

        $entity = array();

        if(strlen($l_wh) > 0){
            $entity['lWh'] = intval($l_wh);
        }

        if(strlen($p_wh) > 0){
            $entity['pWh'] = intval($p_wh);
        }
        if(strlen($uv_wh) > 0){
            $entity['uvWh'] = intval($uv_wh);
        }

        if(strlen($d_cyc) > 0){
            $entity['dCyc'] = intval($d_cyc);
        }

        if(strlen($push_cfg) > 0){
            $entity['pushCfg'] = intval($push_cfg);
        }

        if(!is_array($sp_per) && strlen($sp_per) > 0){
            $entity['spPer'] = json_decode($sp_per);
        }elseif(is_array($sp_per)){
            //ios 传过来到这里获取到的是数组
            $entity['spPer'] = $sp_per;
        }

        if(!is_array($uvc_per) && strlen($uvc_per) > 0){
            $entity['uvcPer'] = json_decode($uvc_per);
        }elseif(is_array($uvc_per)){
            //ios 传过来到这里获取到的是数组
            $entity['uvcPer'] = $uvc_per;
        }

        if(!is_array($l_per) && strlen($l_per) > 0){
            $entity['lPer'] = json_decode($l_per);
        }elseif(is_array($l_per)){
            //ios 传过来到这里获取到的是数组
            $entity['lPer'] = $l_per;
        }

        if(strlen($th) > 0){
            $entity['th'] = intval($th);
        }
        if(strlen($tl) > 0){
            $entity['tl'] = intval($tl);
        }
        if(strlen($tMax) > 0){
            $entity['tMax'] = intval($tMax);
        }
        if(strlen($tmL) > 0){
            $entity['tmL'] = ($tmL);
        }
        if(strlen($mode) > 0){
            $entity['mode'] = intval($mode);
        }
        if(strlen($out_uvc) > 0){
            $entity['outUvc'] = intval($out_uvc);
        }
        if(strlen($out_sp) > 0){
            $entity['outSp'] = intval($out_sp);
        }
        if(strlen($out_l) > 0){
            $entity['outL'] = intval($out_l);
        }

        if(empty($did)){
            $this->apiReturnErr('did参数缺失');
        }


        if(strlen($devLock) > 0){
            $entity['devLock'] = intval($devLock);
        }

        if(count($entity) == 0){
            $this->apiReturnSuc('操作成功');
        }

        $result  = (new Aq806DeviceCtrlAction())->ctrl($did,$entity);
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
        $dbEntity['out_ctrl'] = 0;
        $ctrl_bits = [1,2,4,8,16,32,64,128];
//        Bit0：灯光继电器状态 0：关闭，1：打开
//Bit1：冲浪水泵继电器状态 0：关闭，1：打开
//Bit4：杀菌灯继电器状态 0：关闭，1：打开
//Bit7：手动和自动模式状态 0：手动模式，1：自动模式
        array_key_exists("tMax",$data)  && $dbEntity['t_max'] = $data['tMax'];
        array_key_exists("th",$data)  && $dbEntity['th'] = $data['th'];
        array_key_exists("tl",$data)  && $dbEntity['tl'] = $data['tl'];
        array_key_exists("lPer",$data)  && $dbEntity['l_per'] = $data['lPer'];
        array_key_exists("uvcPer",$data)  && $dbEntity['uvc_per'] = $data['uvcPer'];
        array_key_exists("spPer",$data)  && $dbEntity['sp_per'] = $data['spPer'];
        if(array_key_exists("outL",$data) && $data['outL'] == 1){
          $dbEntity['out_ctrl'] = ($dbEntity['out_ctrl'] | $ctrl_bits[0]);
        }
        if(array_key_exists("outSp",$data) && $data['outSp'] == 1){
            $dbEntity['out_ctrl'] = ($dbEntity['out_ctrl'] | $ctrl_bits[1]);
        }
        if(array_key_exists("outUvc",$data) && $data['outUvc'] == 1){
            $dbEntity['out_ctrl'] = ($dbEntity['out_ctrl'] | $ctrl_bits[4]);
        }
        if(array_key_exists("mode",$data) && $data['mode'] == 1){
            $dbEntity['out_ctrl'] = ($dbEntity['out_ctrl'] | $ctrl_bits[7]);
        }

        array_key_exists("tmL",$data)  && $dbEntity['tm_l'] = $data['tmL'];
        array_key_exists("devLock",$data)  && $dbEntity['dev_lock'] = $data['devLock'];

        array_key_exists("pushCfg",$data)  && $dbEntity['push_cfg'] = $data['pushCfg'];
        array_key_exists("dCyc",$data)  && $dbEntity['d_cyc'] = $data['dCyc'];
        array_key_exists("uvWh",$data)  && $dbEntity['uv_wh'] = $data['uvWh'];
        array_key_exists("pWh",$data)  && $dbEntity['p_wh'] = $data['pWh'];
        array_key_exists("lWh",$data)  && $dbEntity['l_wh'] = $data['lWh'];
        (new Aq806DeviceLogic())->save(['did'=>$did],$dbEntity);
        return $dbEntity;
    }

    public function deviceEvent(){
        $did = $this->_post('did','');
        $code = $this->_post('code','');
        $ph = $this->_post('ph','');
        $t = $this->_post('t','');
        $dyn = $this->_post('dyn','');

        $action = new Aq806DeviceEventAction();
        $result = $action->add($did,$code,$ph,$t,$dyn);

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
                    $result = (new VAq806PhHisAction())->oneDay($did);
                    break;
                case 2:
                    //7天
                    $result = (new VAq806PhHisAction())->lastNDay($did,7);
                    break;
                case 3:
                    //30天
                    $result = (new VAq806PhHisAction())->lastNDay($did,30);
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
                    $result = (new VAq806TempHisAction())->oneDay($did);
                    break;
                case 2:
                    //7天
                    $result = (new VAq806TempHisAction())->lastNDay($did,7);
                    break;
                case 3:
                    //30天
                    $result = (new VAq806TempHisAction())->lastNDay($did,30);
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