<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-03
 * Time: 16:57
 */

namespace app\task\controller;


use app\src\base\helper\PageHelper;
use app\src\crawler\action\RemotePictureHelper;
use app\src\qqav\action\LogAction;
use app\src\qqav\action\VideoAction;
use app\src\qqav\action\VideoTagsAction;
use app\src\qqav\logic\VideoLogic;
use think\Controller;

class VideoTagsTask extends Controller
{
    public function index(){
        set_time_limit(0);
        $page = PageHelper::renew(['page_index'=>0,'page_size'=>100]);
        $result = (new VideoTagsAction())->insertFromVideo($page);
        var_dump($result);
    }
}