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
use think\Controller;

class BaiduTranslate extends Controller
{
    private $appId = "20170703000062287";
    private $appSecret = "sgel8u4tNsl42t_pC9j2";

    /**
     * 翻译演员姓名
     */
    public function actor_name(){

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
    }
}