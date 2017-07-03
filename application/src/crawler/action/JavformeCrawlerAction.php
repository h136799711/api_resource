<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-03
 * Time: 11:30
 */

namespace app\src\crawler\action;


use app\src\base\action\BaseAction;
use app\src\base\helper\ResultHelper;
use app\src\base\helper\ValidateHelper;
use app\src\crawler\JavformeCrawler;
use app\src\crawler\logic\CrawlerUrlLogic;
use app\src\qqav\action\ActorAction;

class JavformeCrawlerAction extends BaseAction
{
    /**
     * 记录信息
     * @param $info
     * @return array
     */
    protected function logInfo($info){
        // name_key,actress_name,title,main_image
        // 搜索key
        $isExist = (new ActorAction())->isExistName($info['actress_name']);

        if($isExist){
           return ResultHelper::error("已经存在相同姓名的");
        }

        $now = time();
        $actorPo = [
            'name_key'=>$info['name_key'],
            'name'=>$info['actress_name'],
            'name_cn'=>'',
            'name_jp'=>'',
            'create_time'=>$now,
            'update_time'=>$now
        ];
        $result = (new ActorAction())->create($actorPo);
        return $result;
    }

    protected function logUrl($url){
        $map = ['url'=>$url];
        $result = (new CrawlerUrlLogic())->getInfo($map);
        if(ValidateHelper::legalArrayResult($result) && $result['info']['url'] == $url){
            return ResultHelper::error("[url] 已处理，无需重复处理");
        }
        $now = time();
        $entity = [
            'url'=>$url,
            'create_time'=>$now,
            'update_time'=>$now,
            'climb_status'=>0
        ];
        $result = (new CrawlerUrlLogic())->add($entity);
        return $result;
    }

    public function parse($url){
        $result = $this->logUrl($url);
        if ($result['status']) {
            // 记录成功的情况下开始解析
            $result = (new JavformeCrawler())->parseHtml($url);
            if($result['status']){
                $map = ['url'=>$url];
                $entity = ['climb_status'=>1];
                (new CrawlerUrlLogic())->save($map,$entity);
                $info = $result['info'];
                $this->logInfo($info);
            }else{
                $map = ['url'=>$url];
                $entity = ['climb_status'=>-1];
                (new CrawlerUrlLogic())->save($map,$entity);
            }
            return $result;
        }
        return $result;
    }
}