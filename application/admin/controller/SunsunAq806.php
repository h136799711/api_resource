<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 16:49
 */

namespace app\admin\controller;
use app\src\sunsun\common\action\UserDeviceAction;
use app\src\sunsun\common\logic\UserDeviceLogic;
use app\src\sunsun\aq806\logic\Aq806DeviceLogic;
use app\src\sunsun\common\logic\DeviceVersionLogic;
/**
 * Class SunsunFilterVat
 * 过滤桶设备
 * @package app\admin\controller
 */
class SunsunAq806 extends Admin
{
    public function index(){


        $map = array();
        $params =false;
        $did   = $this->_param('did', 0);
        $ip    = $this->_param('ip', 0);
        $version   = $this->_param('version', "");
        $startdatetime = $this->_param('startdatetime');
        $startdatetime = strtotime($startdatetime);
        $enddatetime   = $this->_param('enddatetime');
        $enddatetime   = strtotime($enddatetime);
        if(!empty($startdatetime) && !empty($enddatetime)){
            if($startdatetime === FALSE || $enddatetime === FALSE){
                $params = array('startdatetime' =>$startdatetime, 'enddatetime' =>$enddatetime);
                $map['last_login_time'] = array( array('EGT', $startdatetime), array('ELT', $enddatetime), 'and');
                $startdatetime = date("Y-m-d h:i:s", $startdatetime);
                $enddatetime   = date("Y-m-d h:i:s", $enddatetime);
            }else{
                $params = array('startdatetime' => $startdatetime, 'enddatetime' =>$enddatetime);
                $map['last_login_time'] = array( array('EGT', $startdatetime), array('ELT', $enddatetime), 'and');
                $startdatetime = date("Y-m-d h:i:s", $startdatetime);
                $enddatetime   = date("Y-m-d h:i:s", $enddatetime);
            }
        }
       $filterVer=(new DeviceVersionLogic())->getVer(['device_type'=>'S03']);
        $this -> assign('Aq806', $filterVer['info']);
        $this -> assign('version', $version);
        if (!empty($did)){
            $map['did'] = $did;
            $params['did'] = $did;

        }

        if (!empty($version)){
            $map['ver'] = $version;
            $params['ver'] = $version;
        }

        if (!empty($ip)){
            $map['last_login_ip'] = $ip;
            $params['last_login_ip'] = $ip;
        }
        $page = array('curpage' => $this->_param('p', 1), 'size' => config('LIST_ROWS'));
        $order = " last_login_time desc ";
        $result = (new Aq806DeviceLogic())->queryWithPagingHtml($map,$page,$order,$params);
        if($result['status']){
            $this -> assign('did', $did);
            $this -> assign('ip', $ip);
            $this -> assign('startdatetime', $startdatetime);
            $this -> assign('enddatetime', $enddatetime);
            $this->assign('show',$result['info']['show']);
            $this->assign('list',$result['info']['list']);
            return $this->boye_display();
        }else {
            $this -> error($result['info']);
        }
    }
    public function detail(){

        $did   = $this->_param('did', 0);

        $result = (new Aq806DeviceLogic())->getInfo(array("did"=>$did));
        $owner = (new UserDeviceLogic())->getDevOwner(array("did"=>$did));
        if($result['status']){
            $result['info']['l_per'] = json_decode($result['info']['l_per'],true);
            $result['info']['uvc_per'] = json_decode($result['info']['uvc_per'],true);
            $result['info']['sp_per'] = json_decode($result['info']['sp_per'],true);
            $this->assign('devinfo',$result['info']);
            $this->assign('owner',$owner['info']);
            return $this->boye_display();
        }else {
            $this -> error($result['info']);
        }
    }
}