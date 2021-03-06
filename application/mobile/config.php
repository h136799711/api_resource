<?php
return [
    // 默认输出类型
    'default_return_type' => 'html',
    'view_replace_str'    => [
        '__PUBLIC__' => __ROOT__ . '/static/' . request()->module() . '',
        '__JS__'     => __ROOT__ . '/static/' . request()->module() . '/js',
        '__CSS__'    => __ROOT__ . '/static/' . request()->module() . '/css',
        '__IMG__'    => __ROOT__ . '/static/' . request()->module() . '/img',
        '__SELF__' => request()->url(),
        '__CDN__'    => ITBOYE_CDN,

    ],

    'site_url' => 'https://api.app.8raw.com/mobile.php',

];