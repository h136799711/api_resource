<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-16
 * Time: 16:43
 */

namespace app\app\controller;


use app\src\base\enum\ErrorCode;
use app\src\base\helper\PageHelper;
use app\src\base\helper\ValidateHelper;
use app\src\clients\action\ClientsDetailAction;
use app\src\session\action\LoginSessionGetInfoAction;
use app\src\session\logic\LoginSessionLogic;
use app\src\session\po\LoginInfoPo;
use app\src\user\action\UserHelperAction;
use think\controller\Rest;
use think\Request;
use think\Response;

class App extends Rest
{
    protected $appId;
    protected $sessionId;
    protected $response;
    protected $uid;  // 会话id对于的uid
    protected $uidIp;// 用户登录的时候的ip地址
    protected $uidDeviceType;// 用户登录的时候的设备

    public function __construct()
    {
        parent::__construct();
        $this->response = Response::create('','json');
        $this->response ->header("Access-Control-Allow-Origin","*")
            ->header("Access-Control-Allow-Methods","GET, POST,OPTIONS")
            ->header("Access-Control-Allow-Headers","Origin, X-Requested-With, Content-Type, Accept, BY-SESSION-ID,BY-APP-ID ")
            ->header("X-Powered-By","WWW.ITBOYE.COM");
        if(strtoupper($_SERVER['REQUEST_METHOD'])== 'OPTIONS'){
            $this->response->send();
            exit;
        }
        // 初始化参数
        $this->initParams();
        // 初始化配置
        $this->initConfig();
        // 检测必要参数
        $this->checkParams();
    }

    public function checkParams(){

        $result = (new ClientsDetailAction())->detailByAppID($this->appId);
        if(ValidateHelper::legalArrayResult($result) && $result['info']['app_id'] == $this->appId){
            return true;
        }else{
            // app_id 无效
            $this->fail($this->appId.' app_id invalid');
        }

        $result = (new LoginSessionGetInfoAction())->info($this->sessionId);
        if(ValidateHelper::legalArrayResult($result) && $result['info']['log_session_id'] == $this->sessionId){
            $this->uid = $result['info']['uid'];
            $loginInfoPo =  new LoginInfoPo($result['info']['login_info']);
            $this->uidIp = $loginInfoPo->getIp();
            $this->uidDeviceType = $loginInfoPo->getDeviceType();
        }

        return false;
    }

    public function initConfig(){

    }

    public function initParams(){
        $this->uid = -1;
        $this->sessionId = Request::instance()->header('by-session-id');

        $sessionId = $this->_param('session_id','');

        if(!empty($sessionId)) {
            session_start();
            session_id($sessionId);
        }
        if(empty($this->sessionId)){
            $this->sessionId = $sessionId;
        }

        $this->appId = $this->_param('app_id','');
        $appId = Request::instance()->header('by-app-id');
        if(empty($this->appId)){
            $this->appId = $appId;
        }
        if(empty($this->appId)){
            $this->fail('缺少 app_id');
        }
        if(empty($this->sessionId)){
            $this->fail('缺少 sessionId');
        }
    }

    public function returnResult($result){
        if(array_key_exists('status',$result)){
            if($result['status']){
                $this->success($result['info'],'操作成功');
            }else{
                $this->fail($result['info']);
            }
        }else{
            if(array_key_exists('code', $result)) {
                if ($result['code'] === 0) {
                    $this->success($result['data'], '操作成功');
                } else {
                    $this->fail($result['msg']);
                }
            }
        }

        $this->fail('无效的返回数据结构');
    }

    /**
     * 返回 json 格式
     * @param int $code
     * @param string $msg
     * @param array $data
     */
    protected function jsonReturn($code=0,$msg='',$data=[]){
        $this->response->data(['code'=>$code,'msg'=>$msg,'data'=>$data]);
        $this->response->header("Access-Control-Allow-Origin","*")
            ->header("Access-Control-Allow-Methods","GET, POST,OPTIONS")
            ->header("Access-Control-Allow-Headers","Origin, X-Requested-With, Content-Type, Accept, BY-SESSION-ID,BY-APP-ID ")
            ->header("X-Powered-By","WWW.ITBOYE.COM")->send();
        exit(0);
    }

    protected function failNeedLogin($msg){
        $this->jsonReturn(ErrorCode::Api_Need_Login,$msg,[]);
    }

    /**
     * 失败
     * @param $msg
     * @param int $code
     */
    protected function fail($msg,$code=-1){
        $this->jsonReturn($code,$msg,[]);
    }

    /**
     * 成功
     *
     * @param $msg
     * @param array $data
     */
    protected function success($data=[],$msg='操作成功'){
        $this->jsonReturn(0,$msg,$data);
    }

    /**
     * 获取参数
     * @param $key
     * @param string $default
     * @param string $emptyErrMsg
     * @return mixed
     */
    public function _param($key,$default='',$emptyErrMsg=''){
        $value = Request::instance()->param($key,$default);

        if($default == $value && !empty($emptyErrMsg)){
            $this->fail($emptyErrMsg);
        }

        // 特殊增加对于uid参数的检测
        if($key == 'uid'){
            $isExist = (new UserHelperAction())->existUid(intval($value));
            if(!$isExist){
                $this->fail('该用户不存在');
            }
        }

        return $value;
    }

    /**
     * 获取分页参数
     */
    public function getPageHelper(){
        $page_index = $this->_param('page_index',0);
        $page_size  = $this->_param('page_size',10);
        return new PageHelper(['page_index'=>$page_index,'page_size'=>$page_size]);
    }

    public function _empty($method)
    {
        return $this->fail('404 不存在的资源');
    }
}