<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-14
 * Time: 16:25
 */

namespace app\domain;
use app\src\clients\action\ClientsConfigCreateAction;
use app\src\clients\logic\ClientsConfigLogic;

/**
 * Class ClientsConfigDomain
 * 应用配置
 * @package app\domain
 */
class ClientsConfigDomain extends BaseDomain
{
    public function create(){
        $entity = $this->getParams(['title','value','type','app_id','module_code','name']);
        $result = (new ClientsConfigCreateAction())->create($entity);
        $this->exitWhenError($result,true);
    }

    public function del(){

    }
}