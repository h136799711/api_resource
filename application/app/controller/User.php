<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-19
 * Time: 14:00
 */

namespace app\app\controller;
use app\sdk\req\ByUserRequest;
use app\src\securitycode\action\SecurityCodeVerifyAction;
use app\src\user\action\LoginAction;
use app\src\user\enum\LoginDeviceType;
use think\Request;
/**
 * Class User
 * 用户相关接口
 * @package app\app\controller
 */
class User extends App
{

    public function logout(){
        return $this->success('退出成功');
    }

    public function login(){
        $code = 'itboye';
        $country = $this->_param('country','+86');
        $username = $this->_param('username','','缺少用户名');
        $password = $this->_param('password','','缺少密码');
        $device_token = $this->sessionId;
        $header = Request::instance()->header();
        $detect = new \Mobile_Detect($header);
        if($detect->isMobile()){
            $device_type = LoginDeviceType::MOBILE_WEB;
        }else{
            $device_type = LoginDeviceType::PC;
        }
        $result = (new ByUserRequest())->loginByUsernameAndPwd($username,$password,$device_token,$device_type,$country,$code);
        return $this->returnResult($result);
    }

//    public function login(){
//        $verifyCode = $this->_param('verifyCode','','请填写验证码');
//        $verifyId = $this->_param('verifyId','','缺少验证码ID');
//        $username = $this->_param('username','','缺少用户名');
//        $password = $this->_param('password','','缺少密码');
//        // 1. 检查验证码
//        $result = (new SecurityCodeVerifyAction())->verify($verifyId,\app\src\securitycode\model\SecurityCode::TYPE_FOR_LOGIN,$verifyCode,$this->clientId);
//        if(!$result['status']){
//            $this->fail($result['info']);
//        }
//        // 2. 用户登录
//        $device_token = $this->sessionId;
//        $header = Request::instance()->header();
//        $detect = new \Mobile_Detect($header);
//        if($detect->isMobile()){
//            $device_type = LoginDeviceType::MOBILE_WEB;
//        }else{
//            $device_type = LoginDeviceType::PC;
//        }
//
//        $result = (new LoginAction())->login($username,$password,"+86",$device_token,$device_type,"");
//        if(!$result['status']){
//            $this->fail($result['info']);
//        }
//        $userinfo = $result['info'];
//        $this->success($userinfo);
//    }

    /**
     * 用户注册
     */
    public function register(){

        $code = $this->_param('code','','请填写验证码');
        $username = $this->_param('username','','缺少用户名');
        $nickname = $username;
        $password = $this->_param('password','','缺少密码');
        $country = $this->_param('country','','缺少国家区号');
        $result = (new ByUserRequest())->registerByMobile($nickname,$username,$password,$country,$code);
        return $this->returnResult($result);
    }
}