<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-16
 * Time: 16:43
 */

namespace app\app\controller;


use app\src\base\enum\ErrorCode;
use think\controller\Rest;
use think\Request;
use think\Session;

class App extends Rest
{
    protected $clientId;
    protected $sessionId;

    public function __construct()
    {
        parent::__construct();
        // TODO 校验 client_id 是否有效
        $this->initParams();
        $this->initConfig();

        // 检测

    }



    public function initConfig(){

    }

    public function initParams(){

        $this->sessionId = $this->_param('session_id','');
        $header = Request::instance()->header();
        $sessionId = isset($header['sessionid']) ? $header['sessionid'] : null;

        if(!empty($sessionId)) {
            session_start();
            session_id($sessionId);
        }
        if(empty($this->sessionId)){
            $this->sessionId = $sessionId;
        }

        $this->clientId = $this->_param('client_id','');
        $clientId = isset($header['clientid']) ? $header['clientid'] : null;
        if(empty($this->clientId)){
            $this->clientId = $clientId;
        }
        if(empty($this->clientId)){
            $this->fail('缺少 client_id');
        }
        if(empty($this->sessionId)){
            $this->fail('缺少 sessionId');
        }

    }

    public function returnResult($result){
        if($result['status']){
            $this->success($result['info'],'操作成功');
        }else{
            $this->fail($result['info']);
        }
    }

    /**
     * 返回 json 格式
     * @param int $code
     * @param string $msg
     * @param array $data
     */
    protected function jsonReturn($code=0,$msg='',$data=[]){
        $response = $this->response(['code'=>$code,'msg'=>$msg,'data'=>$data], "json",200);

        $response->header("Access-Control-Allow-Origin","*")
            ->header("Access-Control-Allow-Methods","GET, POST,OPTIONS")
            ->header("Access-Control-Allow-Headers","Origin, X-Requested-With, Content-Type, Accept, sessionId,clientId ")

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

        return $value;
    }
}