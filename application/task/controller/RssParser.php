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
        $xml = simplexml_load_string($xml);

        echo var_dump($xml);
    }

    public function testOne(){
        $url = "http://javfor.me/96266.html";
        $html = file_get_contents($url);
        $loader =  new \simple_html_dom();
        $loader->load($html);
        $loader->dump(false);
        echo 'end';
    }
}