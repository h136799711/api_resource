<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-14
 * Time: 11:25
 */

defined("BY_SDK_PATH")  or define('BY_SDK_PATH', dirname(__DIR__ ) . DS);
defined("BY_EXT")  or define('BY_EXT', '.php');
defined("BY_DS")  or define('BY_DS', DIRECTORY_SEPARATOR);


return [
    'by_sdk_version'=>'v1.0.0',

    'lang'=>'zh-cn', // 默认语言包
    'default_timezone'=>'Etc/GMT',// 默认时区 Etc/GMT
     // 接口网关请求地址
    'by_api_gateway_uri'=>'http://apidev.qqav.club/index.php',
     // client_id
    'by_client_id'=>'by58018f50cfcae1',
    // client_secret
    'by_client_secret'=>'cb0bfaf5b9b2f53a216bf518e18fef18',
    // md5_v3 通信算法
    'by_alg'=>'md5_v3',
    // api 是否调试模式
    'by_api_debug'=>false,
];