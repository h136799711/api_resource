<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-14
 * Time: 14:15
 */

namespace app\sdk\req;


use app\sdk\base\ByBaseSdkObj;
use app\sdk\helper\ByCurlHelper;

class ByUserRequest extends ByBaseSdkObj
{
    /**
     * 通过用户名和密码登录
     */
    public function loginByUsernameAndPwd(){

    }

    /**
     * 通过手机号注册
     */
    public function registerByMobile($nickname,$username,$password,$country,$code='itboye'){
        $data = [
            'type'=>'By_User_register',
            'api_ver'=>'102',
            'notify_id'=>self::getNotifyId(),
            'nickname'=>$nickname,
            'username'=>$username,
            'password'=>$password,
            'country'=>$country,
            'code'=>$code,
            'reg_from'=>RegFromEnum::ADMIN_USER_ADD
        ];

        return $this->callremote($data);
    }

}