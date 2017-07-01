<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-01
 * Time: 16:00
 */

namespace app\task\controller;


use think\Controller;

class RssParser extends Controller
{
    public function test(){
        $url = "http://feeds.feedburner.com/JavForMe?format=xml";

        $xml = file_get_contents($url);
        var_dump($xml);
//        simplexml_load_string($url)
    }
}