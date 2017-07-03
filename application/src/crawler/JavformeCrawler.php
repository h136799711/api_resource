<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-03
 * Time: 11:18
 */

namespace app\src\crawler;


use app\src\base\helper\ResultHelper;

class JavformeCrawler extends BaseCrawler
{
    public function parseHtml($url){
        $html = file_get_html($url,false,null,0);
        if($html === false){
            return ResultHelper::error("[$url] 读取失败");
        }
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