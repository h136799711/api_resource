<?php
return [
    // 默认输出类型

    'default_return_type' => 'html',
    'view_replace_str'    => [
        '__PUBLIC__' =>  '/static/' . request()->module() . '',
        '__JS__'     =>  '/static/' . request()->module() . '/js',
        '__CSS__'    =>  '/static/' . request()->module() . '/css',
        '__IMG__'    =>  '/static/' . request()->module() . '/img',
        '__SELF__' => request()->url(),
        '__CDN__'    => ITBOYE_CDN,

    ],


    //'site_url' => 'http://127.0.0.1/github/itboye_hutouben_api/public/mobile.php',

];