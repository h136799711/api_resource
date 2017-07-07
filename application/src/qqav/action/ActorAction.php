<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-03
 * Time: 11:47
 */

namespace app\src\qqav\action;


use app\src\base\action\BaseAction;
use app\src\base\helper\PageHelper;
use app\src\base\helper\ResultHelper;
use app\src\base\helper\ValidateHelper;
use app\src\qqav\logic\ActorLogic;

class ActorAction extends BaseAction
{

    public function isExistName($name){
        $name_key = preg_replace("/\s+/", "", strtolower(trim($name)));
        $result = (new ActorLogic())->getInfo(['name_key'=>$name_key]);
        if(ValidateHelper::legalArrayResult($result) && $result['info']['name_key'] == $name_key){
            return ResultHelper::success($result['info']);
        }
        return ResultHelper::error('不存在');
    }

    /**
     * 创建
     * @param $entity
     * @return array
     */
    public function create($entity){
        return (new ActorLogic())->add($entity);
    }

    /**
     * 查询
     * @param $map
     * @param PageHelper $pageHelper
     * @param bool $order
     * @param bool $params
     * @param bool $field
     * @return array
     */
    public function query($map,PageHelper $pageHelper,$order = false,$params = false,$field = false){
        return (new ActorLogic())->query($map,$pageHelper->queryParam(),$order,$params,$field);
    }
}