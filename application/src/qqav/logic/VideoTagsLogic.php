<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-04
 * Time: 09:59
 */

namespace app\src\qqav\logic;


use app\src\base\logic\BaseLogic;
use think\Db;

class VideoTagsLogic extends BaseLogic
{
    public function queryNotProcessVideo($page=['page_index'=>0,'page_size'=>10]){
        $query = Db::table('qqav_video')
            ->where('id','NOT IN',function($query){
                $query->table('qqav_video_tags')->field('video_id');
            });
        $start = max(intval($page['page_index'])-1,0)*intval($page['page_size']);

        $list = $query -> limit($start,$page['page_size']) ->order('video_id asc') -> select();
        $query = Db::table('qqav_video')
            ->where('id','NOT IN',function($query){
                $query->table('qqav_video_tags')->field('video_id');
            });
        $count = $query -> count();

        $data = [];
        foreach ($list as $vo){
            if(method_exists($vo,"toArray")){
                array_push($data,$vo->toArray());
            }
        }
        return $this -> apiReturnSuc(["count" => $count, "list" => $data]);
    }
}