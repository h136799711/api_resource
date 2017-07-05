<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-03
 * Time: 17:21
 */

namespace app\task\controller;


use app\src\base\helper\PageHelper;
use app\src\qqav\action\LogAction;
use app\src\qqav\action\VideoAction;
use app\src\qqav\logic\LogLogic;
use app\src\qqav\logic\VideoLogic;
use think\Controller;

class Video extends Controller
{
    public function index(){
        $q = $this->request->param('q','');
        $map = [];
        $params = [];
        if(!empty($q)){
            $map['title_en'] = ['like','%'.$q.'%'];
            $params['q'] = $q;
        }
        $p = $this->request->param('p',0);
        $page = ['page_index'=>$p,'page_size'=>30];
        $result = (new VideoLogic())->queryWithPagingHtml($map,PageHelper::renew($page)->queryParam(),"id desc",$params);
        $info = $result['info'];
        $info['list'] = $this->process($info['list']);
        $this->assign('q',$q);
        $this->assign('p',$p);
        $this->assign('list',$info['list']);
        $this->assign('pager',$info['show']);
        return $this->fetch();
    }

    private function process($info){
        foreach ($info as &$vo){
            $url_res = json_decode($vo['url_res'],JSON_OBJECT_AS_ARRAY);
            if(array_key_exists("view_url",$url_res)){
                $vo['view_url'] = $url_res['view_url'];
            }elseif(is_array($url_res) && count($url_res) > 0 && array_key_exists("view_url",$url_res[0])){
                $vo['view_url'] = $url_res[0]['view_url'];
            }else{
                $vo['view_url'] = '#unknown';
            }
        }
        return $info;
    }

    public function log(){
        $level = $this->request->param('level','debug');
        $map = [];
        if(!empty($level)){
            $map['level'] = $level;
        }
        $p = $this->request->param('p',0);
        $page = ['page_index'=>$p,'page_size'=>30];
        $result = (new LogLogic())->queryWithPagingHtml($map,PageHelper::renew($page)->queryParam(),"id asc");
        $info = $result['info'];
        $info['list'] = $this->processLog($info['list']);
        $this->assign('level',$level);
        $this->assign('list',$info['list']);
        $this->assign('pager',$info['show']);
        return $this->fetch();
    }

    private function processLog($list){
        foreach ($list as &$vo){
            $vo['info'] = $this->unicode_decode($vo['info']);
        }
        return $list;
    }

    function unicode_decode($name)
    {
        // 转换编码，将Unicode编码转换成可以浏览的utf-8编码
        $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
        preg_match_all($pattern, $name, $matches);
        if (!empty($matches))
        {
            $name = '';
            for ($j = 0; $j < count($matches[0]); $j++)
            {
                $str = $matches[0][$j];
                if (strpos($str, '\\u') === 0)
                {
                    $code = base_convert(substr($str, 2, 2), 16, 10);
                    $code2 = base_convert(substr($str, 4), 16, 10);
                    $c = chr($code).chr($code2);
                    $c = iconv('UCS-2', 'UTF-8', $c);
                    $name .= $c;
                }
                else
                {
                    $name .= $str;
                }
            }
        }
        return $name;
    }
}