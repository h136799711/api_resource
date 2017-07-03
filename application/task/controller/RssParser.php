<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-01
 * Time: 16:00
 */

namespace app\task\controller;


use app\src\crawler\JavformeCrawler;
use think\Controller;

class RssParser extends Controller
{
    public function test(){
        $url = "http://feeds.feedburner.com/JavForMe?format=xml";

        $xml = file_get_contents($url);
        $xml = simplexml_load_string($xml);

        echo var_dump($xml);
    }

    public function testJavforme(){
        $url = "http://javfor.me/96265.html";
        $crawler = new JavformeCrawler();
        var_dump($crawler->parseHtml($url));
    }

    public function testOne(){
        $url = "http://javfor.me/96266.html";
        $html = file_get_html($url,false,null,0);
        $loader =  new \simple_html_dom();
        $loader->load($html);
        $mainImg = $loader->find("div#my_main_content_box img");
        $src = '';
        $title = '';
        $actressName = '';
        $tags = [];
        if(count($mainImg) > 0){
            $src = $mainImg[0]->src;
        }
        $items = $loader->find("div.post h2");
        if(count($items) > 0){
            $title = $items[0]->plaintext;
        }
        $post = $loader->find("div#information div.post");
        var_dump($post[0]->outertext);
        echo '<br/><br/><br/>';
        if(count($post) > 0){
            $p_items = $post[0]->find("p");
            if(count($p_items) >= 4){
                $actressName = $p_items[2]->find("a")[0]->plaintext;
                $aItems = $p_items[3]->find("a");
                foreach ($aItems as $a){
                    array_push($tags,$a->plaintext);
                }
                echo 'tags = '.json_encode($tags);
            }else{
                echo "无法解析".count($p_items);
            }

        }
        echo '<br/>main image = '.$src;
        echo '<br/>title = '.$title;
        echo '<br/>actress name = '.$actressName;
        $searchKey = preg_replace("/\s+/", "", strtolower(trim($actressName)));
        echo '<br/>search key = '.$searchKey;

    }


}