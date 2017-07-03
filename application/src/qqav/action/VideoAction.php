<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-03
 * Time: 12:12
 */

namespace app\src\qqav\action;


use app\src\base\action\BaseAction;
use app\src\qqav\logic\VideoLogic;

class VideoAction extends BaseAction
{
    /**
     *
     * @param $entity
     * @return array
     */
    public function create($entity){
        if(isset($entity['create_time'])) $entity['create_time'] = time();
        if(isset($entity['update_time'])) $entity['update_time'] = time();

        return (new VideoLogic())->add($entity);
    }
}