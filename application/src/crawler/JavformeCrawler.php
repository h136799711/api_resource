<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-03
 * Time: 11:18
 */

namespace app\src\crawler;


use app\src\base\helper\ResultHelper;
use app\src\qqav\action\LogAction;
use think\Exception;

class JavformeCrawler extends BaseCrawler
{

    /**
     * 解析视频详情页
     * @param $url
     * @return array
     */
    public function parseHtml($url){
        try{
            $html = file_get_html($url,false,null,0);
            if($html === false){
                return ResultHelper::error("[$url] 读取失败");
            }
            $loader =  new \simple_html_dom();
            $loader->load($html);
            $mainImg = $loader->find("div#my_main_content_box img");
            $src = '';
            $title = '';
            $tags = [];
            if(count($mainImg) > 0){
                $src = $mainImg[0]->src;
            }
            $items = $loader->find("div.post h2");
            if(count($items) > 0){
                $title = $items[0]->plaintext;
            }
            $post = $loader->find("div#information div.post");
            if(count($post) > 0){
                $p_items = $post[0]->find("p");
                if(count($p_items) >= 4){
                    $actressName = $p_items[2]->find("a");
                    if(count($actressName) > 0){
                        $actressName = $actressName[0]->plaintext;
                    }else{
                        LogAction::debug("[$url] 找不到演员信息的a标签");
                    }
                    $aItems = $p_items[3]->find("a");
                    foreach ($aItems as $a){
                        array_push($tags,$a->plaintext);
                    }
                }else{
                    return ResultHelper::error("解析失败，没有4个p标签");
                }

            }else{
                return ResultHelper::error("解析失败，没有div#information div.post");
            }
            $name_key = preg_replace("/\s+/", "", strtolower(trim($actressName)));
            $relate_post = $loader->find("div#related_posts a.related_a");
            $urls = [];
            foreach ($relate_post as $vo){
                $href = $vo->href;
                if(strpos($href,"http") === false){
                    $href = "http://javfor.me".$href;
                }
                array_push($urls,$href);
            }

            $entity = [];
            $entity['name_key'] = $name_key;
            $entity['actress_name'] = $actressName;
            $entity['title'] = $title;
            $entity['main_image'] = $src;
            $entity['tags'] = $tags;
            $entity['relate_urls'] = $urls;
            return ResultHelper::success($entity);
        }catch (Exception $e){
            return ResultHelper::error($e->getMessage());
        }
    }
}