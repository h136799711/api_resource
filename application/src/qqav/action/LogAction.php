<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-03
 * Time: 15:59
 */

namespace app\src\qqav\action;


use app\src\base\action\BaseAction;
use app\src\qqav\logic\LogLogic;

class LogAction extends BaseAction
{
    public static function debug($info){
        if(!is_string($info)){
            $info = json_encode($info);
        }
        $entity = [
            'level'=>'debug',
            'info'=>json_encode($info),
            'create_time'=>time()
        ];
        return (new LogLogic())->add($entity);
    }
}