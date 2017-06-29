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
use app\src\user\enum\RegFromEnum;

class ByUserRequest extends ByBaseSdkObj
{
    /**
     * 通过用户名和密码登录
     * @param $username
     * @param $password
     * @param $device_token
     * @param $device_type
     * @param $country
     * @param $code
     * @return array
     */
    public function loginByUsernameAndPwd($username,$password,$device_token,$device_type,$country,$code){
        $data = [
            'type'=>'By_User_Login',
            'api_ver'=>'102',
            'notify_id'=>self::getNotifyId(),
            'role'=>'',
            'device_token'=>$device_token,
            'device_type'=>$device_type,
            'username'=>$username,
            'password'=>$password,
            'country'=>$country,
            'code'=>$code
        ];

        return $this->callRemote($data);
    }

    /**
     * 通过手机号注册
     * @param $nickname
     * @param $username
     * @param $password
     * @param $country
     * @param string $code
     * @return array
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

        return $this->callRemote($data);
    }

}