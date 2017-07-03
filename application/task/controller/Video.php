<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-03
 * Time: 17:21
 */

namespace app\task\controller;


use app\src\base\helper\PageHelper;
use app\src\qqav\action\VideoAction;
use app\src\qqav\logic\VideoLogic;
use think\Controller;

class Video extends Controller
{
    public function index(){
        $map = [];
        $p = $this->request->param('p',0);
        $page = ['page_index'=>$p,'page_size'=>30];
        $result = (new VideoLogic())->queryWithPagingHtml($map,PageHelper::renew($page)->queryParam(),"id asc");
        $info = $result['info'];
        $this->assign('list',$info['list']);
        $this->assign('pager',$info['show']);
        return $this->fetch();
    }
}