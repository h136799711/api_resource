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
        if($result['status'] && count($result['info']['list']) > 0){
            $now = time();
            $list = $result['info']['list'];
            $allEntity = [];
            foreach ($list as $vo){
                $video_id = $vo['id'];
                $tags = explode(",",$vo['tags']);
                foreach ($tags as $tag){
                    array_push($allEntity,[
                        'video_id'=>$video_id,
                        'create_time'=>$now,
                        'update_time'=>$now,
                        'tag_en'=>$tag,
                        'tag_cn'=>'',
                    ]);
                }
            }
            if(count($allEntity) > 0){
                $result = (new VideoTagsLogic())->addAll($allEntity);
            }
        }

        return $result;
    }
}