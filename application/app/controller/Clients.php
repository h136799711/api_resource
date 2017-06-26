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

class Clients extends App
{
    public function create(){
        $name = $this->_param('name','application');
        $uid  = $this->_param('uid','');
        $note = $this->_param('note','');
        $result = (new ClientsCreateAction())->create($uid,$name,$note);
        $this->returnResult($result);
    }

    public function delete(){
        $id = $this->_param('id','');
        $client_id = $this->_param('client_id','');
        if(empty($id) && empty($client_id)){
            $this->fail('缺少参数');
        }
        $result = [];
        if(!empty($id)){
            $result = (new ClientsDeleteAction())->deleteByID($id);
        }
        if(!empty($client_id)){
            $result = (new ClientsDeleteAction())->deleteByClientID($$client_id);
        }
        $this->returnResult($result);
    }

    public function update(){

    }

    public function query(){

    }
}