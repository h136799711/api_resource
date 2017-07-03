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
        $q = $this->request->param('q','');
        $map = [];
        if(!empty($q)){
            $map['title_en'] = ['like','%'.$q.'%'];
        }
        $p = $this->request->param('p',0);
        $page = ['page_index'=>$p,'page_size'=>30];
        $result = (new VideoLogic())->queryWithPagingHtml($map,PageHelper::renew($page)->queryParam(),"id asc");
        $info = $result['info'];
        $info['list'] = $this->process($info['list']);
        $this->assign('q',$q);
        $this->assign('list',$info['list']);
        $this->assign('pager',$info['show']);
        return $this->fetch();
    }

    private function process($info){
        foreach ($info as $vo){
            $url_res = json_decode($vo['url_res']);
            if(array_key_exists("view_url",$url_res)){
                $vo['view_url'] = $url_res['view_url'];
            }elseif(is_array($url_res) && count($url_res) > 0 && array_key_exists("view_url",$url_res[0])){
                $vo['view_url'] = $url_res[0]['view_url'];
            }
        }
        return $info;
    }
}