<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017-09-28
 * Time: 15:08
 */

namespace app\index\controller;


use think\controller\Rest;

class Ip extends Rest
{
    public function location(){
        $ip = $this->_post('ip','');
    }
}