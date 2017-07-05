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
    public static function logDebugResult($result){
        if(array_key_exists("status",$result) && $result['status'] === false){
            self::debug($result['info']);
        }
    }

    public static function debug($info){
        if(!is_string($info)){
            $info = json_encode($info);
        }
        $map = [];
        $map['create_time'] = ['lt',time() - 24 * 3600];
        (new LogLogic())->delete($map);
        $entity = [
            'level'=>'debug',
            'info'=>json_encode($info),
            'create_time'=>time()
        ];
        return (new LogLogic())->add($entity);
    }
}