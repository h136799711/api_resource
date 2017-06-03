<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/4
 * Time: 13:48
 */

namespace app\admin\controller;


use app\src\admin\helper\AdminConfigHelper;
use app\src\base\facade\AccountFacade;
use app\src\user\action\ResetPwdAction;
use app\src\user\facade\DefaultUserFacade;

class Account extends Admin
{
    public function updatepassword(){
        if(IS_GET){
            return $this->boye_display();
        }
        //获取参数
        $password   =  $this->_param('old','');
        empty($password) && $this->error(L('ERR_NEED_OLD_PASSWORD'));
        $new_pwd = $this->_param('password');
        empty($new_pwd) && $this->error(L('ERR_NEED_NEW_PASSWORD'));
        $re_pwd = $this->_param('repassword');
        empty($re_pwd) && $this->error(L('ERR_NEED_CONFIRM_PASSWORD'));

        if($new_pwd !== $re_pwd){
            $this->error(L('ERR_NEED_SAME_PASSWORD'));
        }
        $res    = (new AccountFacade(new DefaultUserFacade()))->updatePwd(['id'=>UID],$new_pwd);
        if($res['status']){
            $this->success("操作成功！请重新登录",url('Admin/Index/logout'));
        }else{
            $this->error($res['info']);
        }
    }

    public function resetPwd(){
        $id = $this->_param('id','');
        $defaultPassword = AdminConfigHelper::getValue('sys_default_password');
        if(empty($defaultPassword) || strlen($defaultPassword) > 24){
            $this->error('请先配置正确的默认密码（长度小于24）');
        }
        $result = (new ResetPwdAction())->reset($id,$defaultPassword);

        if($result['status']){
            $this->success("操作成功！");
        }else{
            $this->error($result['info']);
        }
    }

}