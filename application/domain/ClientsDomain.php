<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-14
 * Time: 16:25
 */

namespace app\domain;

/**
 * Class ClientsDomain
 * 应用
 * @package app\domain
 */
class ClientsDomain extends BaseDomain
{
    public function add(){
        $entity = $this->getParams(['uid','client_name','title']);
        $entity['user_id'] = UID;
        $entity['client_id'] = "by".uniqid().UID;
        $entity['client_secret'] =  md5(uniqid());
    }

    public function del(){

    }
}