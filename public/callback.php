<?php
// [ 应用入口文件 ]

// 绑定当前访问到index模块
define('BIND_MODULE','callback');

define('APP_PATH', __DIR__ . '/../application/');


require_once APP_PATH . 'common_entry.php';