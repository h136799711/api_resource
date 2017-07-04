<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-04
 * Time: 09:59
 */

namespace app\src\qqav\action;


use app\src\base\action\BaseAction;
use app\src\base\helper\PageHelper;
use app\src\qqav\logic\VideoTagsLogic;

class VideoTagsAction extends BaseAction
{
    public function insertFromVideo(PageHelper $pageHelper){
        $result = (new VideoTagsLogic())->queryNotProcessVideo($pageHelper->toPageParam());

        return $result;
    }
}