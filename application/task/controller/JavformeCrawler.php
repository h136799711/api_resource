<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-03
 * Time: 15:14
 */

namespace app\task\controller;


use app\src\crawler\action\JavformeCrawlerAction;
use app\src\crawler\logic\CrawlerUrlLogic;
use think\Controller;

class JavformeCrawler extends Controller
{
    public function start(){
        $map = ['climb_status'=>0];
        $page = ['curpage'=>0,'size'=>100];
        $result = (new CrawlerUrlLogic())->query($map,$page);
        $info = $result['info'];
        if(array_key_exists("list",$info) && count($info['list']) > 0){
            $crawler = new JavformeCrawlerAction();
            foreach ($info['list'] as $urlItem){
                $url = $urlItem['url'];
                $crawler->parse($url);
            }
        }
    }
}