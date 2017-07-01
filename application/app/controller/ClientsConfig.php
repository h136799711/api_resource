<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-30
 * Time: 16:22
 */

namespace app\app\controller;


use app\sdk\req\ByClientsConfigRequest;
use think\Request;

class ClientsConfig extends App
{
    public function create(){
        $post = Request::instance()->post();
        $result = (new ByClientsConfigRequest())->create($post);
        return $this->returnResult($result);
    }

}