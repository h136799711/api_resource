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
use app\src\crawler\constants\CrawlerUrlType;
use app\src\crawler\JavformeCrawler;
use app\src\crawler\logic\CrawlerUrlLogic;
use app\src\qqav\action\ActorAction;
use app\src\qqav\action\LogAction;
use app\src\qqav\action\VideoAction;
use app\src\qqav\action\VideoTagsAction;
use app\src\qqav\logic\VideoActorsLogic;
use app\src\qqav\logic\VideoLogic;
use app\src\qqav\logic\VideoTagsLogic;

class JavformeCrawlerAction extends BaseAction
{
    protected function logMoreUrl($urls){
        $now = time();
        $allEntity = [];
        foreach ($urls as $url){
            $map = ['url'=>$url];
            $result = (new CrawlerUrlLogic())->getInfo($map);
            if(ValidateHelper::legalArrayResult($result) && $result['info']['url'] == $url) {
                //已经存在则不添加了
                continue;
            }
                array_push($allEntity,[
                'url'=>$url,
                'create_time'=>$now,
                'update_time'=>$now,
                'climb_status'=>0,
                'url_type'=>CrawlerUrlType::JAV_FOR_ME,
            ]);
        }
        if(count($allEntity) > 0){
            $result = (new CrawlerUrlLogic())->addAll($allEntity);
        }
    }

    protected function logTags($video_id,$tags){
        if($video_id <= 0) return ResultHelper::error('video_id 无效');
        $now = time();
        $allEntity = [];
        foreach ($tags as $tag){
            $tag = trim($tag);
            array_push($allEntity,[
                'video_id'=>$video_id,
                'create_time'=>$now,
                'update_time'=>$now,
                'tag_en'=>$tag,
                'tag_cn'=>'',
                'tag_md5'=>md5($tag)
            ]);
        }
        if(count($allEntity) > 0){
            $result = (new VideoTagsLogic())->addAll($allEntity);
        }else{
            return ResultHelper::error("video_id = ".$video_id.'没有tags');
        }
        return $result;
    }

    protected function logVideo($info,$url){
        $video_key = md5($info['title']);
        $map = ['video_key'=>$video_key];
        $result = (new VideoLogic())->getInfo($map);
        if(ValidateHelper::legalArrayResult($result) && $result['info']['video_key'] == $video_key){
            return  ResultHelper::success($result['info']['id']);
        }
        $videoEntity = [
            'main_image'=>$info['main_image'],
            'title_en'=>$info['title'],
            'title_cn'=>'',
            'from_url'=>'http://javfor.me',
            'url_res'=>json_encode([[
                'view_url'=>$url
            ]]),
            'tags'=>implode(",",$info['tags']),
            'create_time'=>time(),
            'update_time'=>time(),
            'video_key'=>$video_key
        ];

        $result = (new VideoAction())->create($videoEntity);

        return $result;
    }

    protected function logActor($info){
        $actor_id = 0;
        if(!empty($info['actress_name'])){
            // 只有存在演员姓名时
            $actorResult = (new ActorAction())->isExistName($info['actress_name']);

            if(ResultHelper::isSuccess($actorResult)){
                $actor_id = $actorResult['info']['id'];
                return ResultHelper::success($actor_id);
            }else{
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
                LogAction::logDebugResult($result);
                if($result['status']){
                    return ResultHelper::success($result['info']);
                }
            }
        }
        return ResultHelper::error($actor_id);
    }

    /**
     * 记录信息
     * @param $info
     * @param $url
     * @return array
     */
    protected function logInfo($info,$url){
        // name_key,actress_name,title,main_image,tags
        // 搜索key
        $this->logMoreUrl($info['relate_urls']);
        $video_id = 0;
        $videoResult = $this->logVideo($info,$url);
        LogAction::logDebugResult($videoResult);

        if($videoResult['status']){
            $video_id = $videoResult['info'];
            $result = $this->logTags($video_id,$info['tags']);
            LogAction::logDebugResult($result);
        }

        $actorResult = $this->logActor($info);
        $actor_id = $actorResult['info'];
        if($actor_id > 0 && $video_id > 0){
            // 同时大于0
            $result = (new VideoActorsLogic())->add([
                'actor_id'=>$actor_id,
                'video_id'=>$video_id
            ]);
            LogAction::logDebugResult($result);
        }


        return $videoResult;
    }

    protected function logUrl($url){
        $map = ['url'=>$url];
        $result = (new CrawlerUrlLogic())->getInfo($map);
        if(ValidateHelper::legalArrayResult($result) && $result['info']['url'] == $url){
            if($result['info']['climb_status'] == 1){

                LogAction::debug("[$url] 已处理，无需重复处理");
                return ResultHelper::error("[$url] 已处理，无需重复处理");
            }else{
                $map = ['id'=>$result['info']['id']];
                $entity = ['climb_status'=>1];
                $result = (new CrawlerUrlLogic())->save($map,$entity);
                return $result;
            }
        }
        $now = time();
        $entity = [
            'url'=>$url,
            'create_time'=>$now,
            'update_time'=>$now,
            'climb_status'=>0,
            'url_type'=>CrawlerUrlType::JAV_FOR_ME
        ];
        $result = (new CrawlerUrlLogic())->add($entity);
        return $result;
    }

    /**
     * 1. 记录url，防止重复
     * 2. 记录解析后的演员信息
     * 3. 记录解析后的视频信息
     * @param $url
     * @return array
     */
    public function parse($url){
        $result = $this->logUrl($url);
        LogAction::logDebugResult($result);
        if ($result['status']) {
            // 记录成功的情况下开始解析
            $result = (new JavformeCrawler())->parseHtml($url);
            LogAction::logDebugResult($result);
            $map = ['url'=>$url];
            if($result['status']){
                $info = $result['info'];
                $result =  $this->logInfo($info,$url);
                if($result['status']){
                    $entity = ['climb_status'=>1];
                }else{
                    $entity = ['climb_status'=>-1];
                }
            }else{
                $entity = ['climb_status'=>-1];
            }
            $entity['update_time'] = time();
            (new CrawlerUrlLogic())->save($map,$entity);
            return $result;
        }
        return $result;
    }
}