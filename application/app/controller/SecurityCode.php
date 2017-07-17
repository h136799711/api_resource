<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-19
 * Time: 09:37
 */

namespace app\app\controller;


use app\src\base\helper\ValidateHelper;
use app\src\securitycode\action\SecurityCodeCreateAction;
use app\src\securitycode\enum\CodeCreateWayEnum;
use app\src\securitycode\logic\SecurityCodeLogic;
use app\src\verify\think\DbVerify;

class SecurityCode extends App
{

    /**
     * 图片类型验证码创建并返回图片包含的验证码数据
     * 仅用于登录
     * @author hebidu <email:346551990@qq.com>
     * @modify 2017-06-19 14:03:30
     */
    public function image_create(){
        $acceptor = $this->sessionId;
        $result = (new SecurityCodeCreateAction())->create($this->appId,$acceptor,\app\src\securitycode\model\SecurityCode::TYPE_FOR_LOGIN,CodeCreateWayEnum::ALPHA_AND_NUMBER,4);
        if(ValidateHelper::legalArrayResult($result)){
            unset($result['info']['code']);
        }
        $this->returnResult($result);
    }

    /**
     * 根据验证码id得到 图片
     */
    public function image_src(){
        $id = $this->_param('id',0);
        $result = (new SecurityCodeLogic())->getInfo(['id'=>$id]);
        $config = ['fontSize'=>16,'imageH'    =>  35,'imageW'    =>  140];
        $verify = new DbVerify($config);
        if(ValidateHelper::legalArrayResult($result)){
            $code = $result['info']['code'];
            $status = $result['info']['status'];
            if($status == 0){
                $verify->entry($code);
                exit;
            }
        }

        $verify->entry("");
    }
}