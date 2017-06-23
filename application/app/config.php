<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-19
 * Time: 19:59
 */
return [
    //mysql session配置
    'session'                => [
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => 'itboye_sid',
        // SESSION 前缀
        'prefix'         => 'itboye',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => false,
    ]
];