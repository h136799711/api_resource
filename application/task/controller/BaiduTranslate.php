<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-03
 * Time: 18:12
 */

namespace app\task\controller;


use app\src\baidu_api\fanyi\BDTranslateLangType;
use app\src\baidu_api\fanyi\BDTranslater;
use app\src\base\helper\PageHelper;
use app\src\qqav\action\ActorAction;
use app\src\qqav\action\LogAction;
use app\src\qqav\logic\ActorLogic;
use app\src\qqav\logic\VideoTagsLogic;
use think\Controller;

class BaiduTranslate extends Controller
{
    private $appId = "20170703000062287";
    private $appSecret = "sgel8u4tNsl42t_pC9j2";

//    public function video_tag(){
//        echo '视频标签英文转中文';
//        set_time_limit(0);
//        $order = "id asc";
//        $map = ['tag_cn'=>''];
//        $page = ['page_index'=>0,'page_size'=>5000];
//        $result = (new VideoTagsLogic())->query($map,PageHelper::renew($page)->queryParam(),$order);
//        $info = $result['info'];
//        if(array_key_exists("list",$info) && count($info['list']) > 0){
//            $translate = (new BDTranslater($this->appId,$this->appSecret));
//            foreach ($info['list'] as $tag){
//                $id = $tag['id'];
//                $src = $tag['tag_en'];
//                $result = $translate->translate($src,BDTranslateLangType::En,BDTranslateLangType::Zh);
//                if(array_key_exists("trans_result",$result)){
//                    $trans_result = $result['trans_result'];
//                    $tag_cn = $trans_result[0]['dst'];
//                    (new VideoTagsLogic())->save(['id'=>$id],['tag_cn'=>$tag_cn]);
//                }elseif(array_key_exists("error_code",$result)){
//                    LogAction::debug($result['error_msg']);
//                }
//            }
//        }
//        return 'complete';
//    }

    /**
     * 翻译演员姓名
     */
    public function actor_name(){
        set_time_limit(0);
        $order = "id asc";
        $map = [];
        $page = ['page_index'=>0,'page_size'=>500];
        $result = (new ActorAction())->query($map,PageHelper::renew($page),$order);
        $info = $result['info'];
        if(array_key_exists("list",$info) && count($info['list']) > 0){

            $translater = (new BDTranslater($this->appId,$this->appSecret));
            foreach ($info['list'] as $actor){
                $id = $actor['id'];
//                $en = $actor['name'];
//                $result = $translater->translate($en,BDTranslateLangType::En,BDTranslateLangType::Jp);
                $name_jp = $actor['name_jp'];
//                if(array_key_exists("trans_result",$result)){
//                    $trans_result = $result['trans_result'];
//                    $name_jp = $trans_result[0]['dst'];
//                    (new ActorLogic())->save(['id'=>$id],['name_jp'=>$name_jp]);
//                }elseif(array_key_exists("error_code",$result)){
//                    LogAction::debug($result['error_msg']);
//                }
                if(!empty($name_jp)){
                    $result = $translater->translate($name_jp,BDTranslateLangType::Jp,BDTranslateLangType::Zh);
                    if(array_key_exists("trans_result",$result)){
                        $trans_result = $result['trans_result'];
                        $name_cn = $trans_result[0]['dst'];
                        (new ActorLogic())->save(['id'=>$id],['name_cn'=>$name_cn]);
                    }elseif(array_key_exists("error_code",$result)){
                        LogAction::debug($result['error_msg']);
                    }
                }
            }
        }
    }

    /**
     * 中文转英文
     */
    public function en2cn(){
        $q = $this->request->param('q');
        $result = (new BDTranslater($this->appId,$this->appSecret))->translate($q,BDTranslateLangType::En,BDTranslateLangType::Zh);
        var_dump($result);
        $result = (new BDTranslater($this->appId,$this->appSecret))->translate($q,BDTranslateLangType::En,BDTranslateLangType::Jp);
        var_dump($result);
        $q = 'かすみりさ';
        $result = (new BDTranslater($this->appId,$this->appSecret))->translate($q,BDTranslateLangType::Jp,BDTranslateLangType::Zh);
        var_dump($result);
    }
}