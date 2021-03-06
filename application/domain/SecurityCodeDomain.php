<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-17
 * Time: 11:36
 */

namespace app\domain;


use app\admin\controller\Email;
use app\sdk\helper\ByConfigHelper;
use app\src\base\helper\ResultHelper;
use app\src\base\helper\ValidateHelper;
use app\src\email\action\EmailSendAction;
use app\src\i18n\helper\LangHelper;
use app\src\jobs\JobsHelper;
use app\src\message\facade\MessageEntity;
use app\src\message\facade\MessageFacade;
use app\src\securitycode\action\SecurityCodeCreateAction;
use app\src\securitycode\action\SecurityCodeVerifyAction;
use app\src\securitycode\enum\CodeTypeEnum;
use app\src\securitycode\logic\SecurityCodeLogic;
use app\src\securitycode\model\SecurityCode;
use app\src\user\logic\UcenterMemberLogic;

class SecurityCodeDomain extends BaseDomain
{
    /**
     * 验证码检测
     */
    public function check(){
        $id = $this->_post('id','');
        if(empty($id)){
            $this->apiReturnErr('缺少id参数');
        }

        $type = $this->_post("code_type","",lang('code_type_need'));
        $code = $this->_post('code','', lang('code_need'));

        $action = (new SecurityCodeVerifyAction());
        $result = $action->verify($id,$type,$code,$this->client_id);
        $this->returnResult($result);
    }

    /**
     * 创建
     * @author hebidu <email:346551990@qq.com>
     */
    public function create(){
        $acceptor = $this->_post('acceptor','', LangHelper::lackParameter('acceptor'));
        $type = $this->_post("code_type","",lang('code_type_need'));
        $code_length = $this->_post('code_length','6');
        $code_create_way = $this->_post('code_create_way','only_number');
        $time = strtotime(date('Y-m-d',time()));
        $result = (new SecurityCodeLogic())->count(['acceptor'=>$acceptor,'create_time'=>['gt',$time]]);
        if($result['status'] && intval($result['info']) > 100){
            $this->apiReturnErr('调用超过限制（100次）');
        }
        $action = (new SecurityCodeCreateAction());
        $result = $action->create($this->client_id,$acceptor,$type,$code_create_way,$code_length);


        $this->returnResult($result);
    }

    /**
     * 校验验证码是否正确,该校验不会让验证码失效
     * @author hebidu <email:346551990@qq.com>
     */
    public function verify(){

        $country = $this->_post('country','', lang('country_tel_number_need'));
        $code = $this->_post('code','', lang('code_need'));
        $mobile = $this->_post('mobile','', lang('mobile_need'));
        $type = $this->_post("code_type","",lang('code_type_need'));

        $this->mobileCheck($country,$mobile,$type);

        $acceptor = $country.$mobile;
        //1. 校验验证码
        $is_clear_code = false;
        $action = new SecurityCodeVerifyAction();
        $result = $action->verify($acceptor,$type,$code,$this->client_id,$is_clear_code);
        $this->returnResult($result);
    }

    /**
     * 验证码发送接口
     * @author hebidu <email:346551990@qq.com>
     */
    public function send(){

        $this->checkVersion("101");

        $country = $this->_post("country","",lang('country_tel_number_need'));
        $mobile = $this->_post("mobile","",lang('mobile_need'));
        $type = $this->_post("code_type","",lang('code_type_need'));
        $send_type = $this->_post("send_type","");
        $notes = SecurityCode::getTypeDesc($type);
        if($notes == "未知"){
            $this->apiReturnErr(lang("invalid_parameter",['param'=>'code_type']));
        }
        $this->mobileCheck($country,$mobile,$type);
        $action = new SecurityCodeCreateAction();
        $accepter = $country.$mobile;
        $result = $action->create($this->client_id,$accepter,$type);
        $this->exitWhenError($result);
        $code = $result['info'];

        //根据send_type 来调用返回验证码的渠道
        if(empty($send_type) || $send_type == CodeTypeEnum::Sms){
            //调用短信接口进行 发送
            $msgFacade = new MessageFacade();
            $msg = new MessageEntity();
            $msg->setScene("[".$notes."]");
            $msg->setCode($code);
            $msg->setCountry($country);
            $msg->setMobile($mobile);

            $result = $msgFacade->send($msg);
        }

        if(!empty($send_type) && $send_type == CodeTypeEnum::ALERT){
            $result = ResultHelper::success($code);
        }

        if($result['status']){
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr(lang('fail').'=>'.$result['info']);
        }

    }

    /**
     * 邮件验证码发送接口 待与send整合 临时用
     * @author hebidu <email:346551990@qq.com>
     */
    public function sendEmail(){

        $this->checkVersion("101");
        $email = $this->_post("email","",lang('email_need'));
        $type = $this->_post("code_type","",lang('code_type_need'));
      //  $send_type = $this->_post("send_type","");
        $notes = SecurityCode::getTypeDesc($type);
        if($notes == "未知"){
            $this->apiReturnErr(lang("invalid_parameter",['param'=>'code_type']));
        }

        if($type == SecurityCode::TYPE_FOR_REGISTER){

            $result = (new UcenterMemberLogic())->getInfo(['email'=>$email]);

            if(ValidateHelper::legalArrayResult($result)){
                $this->apiReturnErr(lang('tip_email_registered'));
            }

        }

        if(!ValidateHelper::isEmail($email)){
            $this->apiReturnErr(lang("invalid_email"));
        }

        $action = new SecurityCodeCreateAction();
        $accepter = $email;
        $result = $action->create($this->client_id,$accepter,$type);
        $this->exitWhenError($result);
        $code = $result['info'];

        //根据send_type 来调用返回验证码的渠道
        //$data以后调用模板
        $title='森森'.$notes.'验证码';
//        $data=array(
//            'to_email'=>$email,
//            'content'=>$code,
//            'title'=>$title
//        );
        //调用邮箱接口进行 发送
//        JobsHelper::pushEmailJob($data);
        (new EmailSendAction())->send($email,$title,$code);
        $this->apiReturnSuc(lang('success'));
          //  $result = (new EmailSendTemplateAction())->regSend($email,$code);
//        if($result['status']){
//            $this->apiReturnSuc($result['info']);
//        }else{
//            $this->apiReturnErr(lang('fail').'=>'.$result['info']);
//        }

    }
    /**
     * 检测手机号和用途的合法性
     * @param $country
     * @param $mobile
     * @param $type
     */
    private function mobileCheck($country,$mobile,$type){
        $map = array(
            'mobile' => $mobile,
            'country_no'=>$country
        );

        $logic = new UcenterMemberLogic();

        $result = $logic->getInfo($map);

        if($type == SecurityCode::TYPE_FOR_REGISTER){
            if ($result['info'] != null) {
                $this->apiReturnErr(lang('tip_mobile_registered'));
            }
        }elseif($type == SecurityCode::TYPE_FOR_UPDATE_PSW ){
            if ($result['info'] == null) {
                $this->apiReturnErr(lang('tip_mobile_unregistered'));
            }
        }
    }

}