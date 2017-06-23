<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-14
 * Time: 13:39
 */

namespace app\sdk\helper;


class ByConfigHelper
{

    public static $config;
    private static $instance;

    public function __construct()
    {
        if (empty(self::$config)) {
            self::$config = include(dirname(__DIR__) . '/config/config.php');
        }
    }

    public static function getInstance(){
        if(!(self::$instance)){
            self::$instance = new ByConfigHelper();
        }
        return self::$instance;
    }


    /**
     * api 网关地址
     * @return string 网关地址
     */
    public static function getApiGatewayUri(){
        return self::getValue('by_api_gateway_uri');
    }

    /**
     * client_id
     * @return string 应用标识
     */
    public static function getClientId(){
        return self::getValue('by_client_id');
    }

    /**
     * client_secret
     * @return string 应用密钥
     */
    public static function getClientSecret(){
            return self::getValue('by_client_secret');
    }

    /**
     * 获取SDK版本
     */
    public static function getSdkVersion(){
        return self::getValue('by_sdk_version');
    }

    private static function getValue($name){
        if(array_key_exists($name,self::$config)){
            return self::$config[$name];
        }
        return "";
    }
}