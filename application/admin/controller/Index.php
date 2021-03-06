<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

namespace  app\admin\controller;

use app\src\admin\api\po\SecurityCodePo;
use app\src\admin\api\SecurityCodeApi;
use app\src\admin\api\UserApi;
use app\src\admin\controller\BaseController;
use app\src\admin\helper\AdminConfigHelper;
use app\src\admin\helper\AdminFunctionHelper;
use app\src\admin\helper\AdminSessionHelper;
use app\src\securitycode\model\SecurityCode;
use app\src\user\action\UserLogoutAction;
use app\src\user\enum\LoginDeviceType;
use think\Request;

/**
 * Class PublicController
 * @package Admin\Controller
 */
class Index extends BaseController {

    /**
     * 测试账号
     */
    private $test_account = array(
        'itboye'=>array('pwd'=>'123456789','roledesc'=>'总管理员'),
        'hutouben'=>array('pwd'=>'123456','roledesc'=>'后台操作员')
    );

    /**
     * 注销\登出
     */
    public function logout() {
        if(request()->isAjax()){
            $uid = AdminSessionHelper::getUserId();
            $auto_login_code = AdminSessionHelper::getAutoLoginCode();

            (new UserLogoutAction())->logout($uid,$auto_login_code);

            //会话
            AdminSessionHelper::logout();
            $this->success('退出成功');
        }
        if(request()->isGet()){
            $uid = AdminSessionHelper::getUserId();
            $auto_login_code = AdminSessionHelper::getAutoLoginCode();

            (new UserLogoutAction())->logout($uid,$auto_login_code);

            //会话
            AdminSessionHelper::logout();
            $this -> redirect(url('index/login'));
        }
    }


    /**
	 * 登录检测
	 */
	public function checkLogin() {
	    $IS_DEBUG = AdminConfigHelper::getValue("app_debug");
        if (request()->isAjax()) {

            $verify = request()->post('verify', '', 'trim');
            if(!$IS_DEBUG){
                $result = $this -> check_verify($verify);
                if(!$result['status']){
                    $this->error(L('ERR_VERIFY'),url('index/login'));
                }
            }

            $username =  Request::instance()->post('username', '', 'trim');
            $password = Request::instance()->post('password', '', 'trim');

            if($IS_DEBUG && isset($this->test_account[$username])){
                $password = $this->test_account[$username]['pwd'];
            }
            //TODO: 根据浏览器信息生成一个设备标识
            $result = UserApi::login($username, $password, $this->session_id, LoginDeviceType::PC, "+86");

			//调用成功
			if ($result['status'] && is_array($result['info'])) {
                $user = $result['info'];
                $user['_username'] = $username;

                AdminSessionHelper::setLoginUserInfo($user);

                $this -> success(L('SUC_LOGIN'), url('manager/index'), ['sessionid' => $this->session_id]);

            } else {
                $this -> error($result['info'],url('index/login'));
            }
		}
	}

    private function check_verify($code)
    {
        $acceptor = AdminSessionHelper::getSessionId();
        $po = new SecurityCodePo();
        $po->setAcceptor($acceptor);
        $po->setCodeType(SecurityCode::TYPE_FOR_LOGIN);
        $po->setCode($code);
        $api = new SecurityCodeApi();
        return $api->check($po);
    }

	/**
	 * GET 登录
	 * POST 登录验证
	 */
	public function login() {
		$this -> assignTitle("账号-登录");

        return $this->fetch("default/index/vue-admin");
//        return $this->fetch("learun/". request()->controller().'/'.request()->action());
	}

	/**
	 * 注册页面
	 *
	 * @return 注册页面
	 * @author beibei hebiduhebi@126.com
	 */
	public function register() {
		$this -> assignTitle("账号-注册");
        return $this->fetch();
	}

	/**
	 * 找回密码
	 * @author beibei hebiduhebi@126.com
	 */
	public function forgotPassword() {
		$this -> assignTitle("账号-忘记密码");
		$this -> error("Not implement!");
	}

    protected function _initialize()
    {
        parent::_initialize();

        //获取数据库全部配置
        AdminConfigHelper::init();

        $seo = array(
            'title' => AdminConfigHelper::getValue('WEBSITE_TITLE'),
            'keywords' => AdminConfigHelper::getValue('WEBSITE_KEYWORDS'),
            'description' => AdminConfigHelper::getValue('WEBSITE_DESCRIPTION'));
        $cfg = array(
            'owner' => AdminConfigHelper::getValue('WEBSITE_OWNER'),
            'statisticalcode' => AdminConfigHelper::getValue('WEBSITE_STATISTICAL_CODE'),
            'theme' => AdminFunctionHelper::getSkin(AdminConfigHelper::getValue('DEFAULT_SKIN'))
        );

        if (!defined("APP_VERSION")) {
            //定义版本
            if (defined("APP_DEBUG") && APP_DEBUG) {
                define("APP_VERSION", time());
            } else {
                define("APP_VERSION", AdminConfigHelper::getValue('APP_VERSION'));
            }
        }

        //
        $this->assignVars($seo, $cfg);
    }
}
