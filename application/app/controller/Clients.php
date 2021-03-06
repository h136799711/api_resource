<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-26
 * Time: 16:00
 */

namespace app\app\controller;

use app\src\clients\action\ClientsCreateAction;
use app\src\clients\action\ClientsDeleteAction;
use app\src\clients\action\ClientsDetailAction;
use app\src\clients\action\ClientsQueryAction;
use app\src\clients\action\ClientsResetSecretAction;
use app\src\clients\action\ClientsUpdateAction;

class Clients extends App
{
    /**
     * 创建
     */
    public function create(){
        $name = $this->_param('name','application');
        $uid  = $this->_param('uid','');
        $note = $this->_param('note','');
        $result = (new ClientsCreateAction())->create($uid,$name,$note);
        $this->returnResult($result);
    }

    /**
     * 删除
     */
    public function delete(){
        $id = $this->_param('id','');
        $app_id = $this->_param('app_id','');
        if(empty($id) && empty($app_id)){
            $this->fail('缺少参数');
        }
        $uid = $this->_param('uid','');
        $result = [];
        if(!empty($id)){
            $result = (new ClientsDeleteAction())->deleteByID($id,$uid);
        }elseif(!empty($app_id)){
            $result = (new ClientsDeleteAction())->deleteByAppID($$app_id,$uid);
        }
        $this->returnResult($result);
    }

    /**
     * 更新
     */
    public function update(){
        $id = $this->_param('id','');
        $app_id = $this->_param('app_id','');
        if(empty($id) && empty($app_id)){
            $this->fail('缺少参数');
        }
        $name = $this->_param('name','');
        $note = $this->_param('note','');
        if(empty($name)){
            $this->fail('应用名不能为空');
        }
        $result = [];
        if(!empty($id)) {
            $result = (new ClientsUpdateAction())->updateByID($id,$name,$note);
        }elseif(!empty($app_id)) {
            $result = (new ClientsUpdateAction())->updateByAppID($app_id,$name,$note);
        }

        $this->returnResult($result);
    }

    /**
     * 查询
     */
    public function query(){
        $uid = $this->_param('uid','','缺少用户id');
        $result = (new ClientsQueryAction())->query($uid,'',$this->getPageHelper());
        $this->returnResult($result);
    }

    /**
     * 详情
     */
    public function detail(){

        $id = $this->_param('id','');
        $app_id = $this->_param('app_id','');
        if(empty($id) && empty($app_id)){
            $this->fail('缺少参数');
        }

        $result = [];
        if(!empty($id)) {
            $result = (new ClientsDetailAction())->detailByID($id);
        }elseif(!empty($app_id)) {
            $result = (new ClientsDetailAction())->detailByAppID($app_id);
        }
        $this->returnResult($result);
    }

    public function reset_secret(){

        $id = $this->_param('id','');
        $app_id = $this->_param('app_id','');
        if(empty($id) && empty($app_id)){
            $this->fail('缺少参数');
        }

        $result = [];
        if(!empty($id)) {
            $result = (new ClientsResetSecretAction())->resetSecretByID($id);
        }elseif(!empty($app_id)) {
            $result = (new ClientsResetSecretAction())->resetSecretByAppID($app_id);
        }
        $this->returnResult($result);
    }
}