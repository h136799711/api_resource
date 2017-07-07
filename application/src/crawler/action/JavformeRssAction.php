<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-07
 * Time: 13:47
 */

namespace app\src\crawler\action;


use app\src\base\helper\ResultHelper;
use app\src\base\helper\ValidateHelper;
use app\src\crawler\constants\CrawlerUrlType;
use app\src\crawler\logic\CrawlerUrlLogic;

class JavformeRssAction
{
    public function read($url){
        $xml = file_get_contents($url);
        $xml = simplexml_load_string($xml);
        $items = $xml->xpath("channel/item");
        $now = time();
        $allEntity = [];
        foreach ($items as $item){
            $url = ''.$item->link;
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
            return $result;
        }
        return ResultHelper::error('没有可插入的');
    }
}