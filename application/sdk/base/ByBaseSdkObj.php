<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-14
 * Time: 11:24
 */

namespace app\sdk\base;

use app\sdk\helper\ByConfigHelper;
use app\sdk\helper\ByCurlHelper;
use app\sdk\helper\ByLangHelper;

class ByBaseSdkObj
{
    private static $config;
    private static $curlHelper;

    public function __construct()
    {
        // 读取配置信息
        $instance = ByConfigHelper::getInstance();
        self::$config = $instance::$config;
        var_dump(self::$config);
        self::$curlHelper = new ByCurlHelper(self::$config);
        // 设置语言
        ByLangHelper::setLang(self::$config['lang']);
        // 设置时区
        date_default_timezone_set(self::$config['default_timezone']);
    }

    protected function callRemote($data){
        return self::$curlHelper->callRemote($data);
    }

    protected function getNotifyId(){
        return time();
    }
}