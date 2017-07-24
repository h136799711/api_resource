<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-16
 * Time: 16:38
 */

namespace app\app\controller;


use app\app\helper\AppMenuHelper;
use app\app\helper\SensitiveDataHelper;
use app\src\admin\helper\AdminConfigHelper;
use app\src\admin\helper\AdminSessionHelper;
use app\src\admin\helper\MenuHelper;
use app\src\base\helper\ConfigHelper;
use app\src\base\helper\ValidateHelper;
use app\src\session\action\LoginSessionCheckAction;
use app\src\session\logic\LoginSessionLogic;
use app\src\user\facade\DefaultUserFacade;

class Index extends App
{
    public function user_data(){
        $result = (new LoginSessionLogic())->getInfo(['log_session_id'=>$this->sessionId]);

        if(!ValidateHelper::legalArrayResult($result)){
            $this->failNeedLogin('请重新登录');
        }
        $uid = $result['info']['uid'];

        $result = (new DefaultUserFacade())->getInfo($uid,$this->sessionId);
        if(!ValidateHelper::legalArrayResult($result)){
            $this->failNeedLogin('请重新登录');
        }

        $user_info = $result['info'];
        $menuList = AppMenuHelper::getMenu($uid);

        $UCENTER_PLATFORM = ConfigHelper::getValue('UCENTER_PLATFORM');
        $user_info = [
            'id' => $user_info['id'],
            'username' => $user_info['username'],
            'nickname' => $user_info['nickname'],
            'head' => $user_info['head'],
            'mobile'=> SensitiveDataHelper::hiddenMobile($user_info['mobile'])
        ];
        $data = [
            'menuList' => $menuList,
            'userInfo' => $user_info,
            'platformInfo' => $UCENTER_PLATFORM
        ];
        $this->success($data,'获取成功');
    }


}