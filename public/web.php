<?php
// [ 应用入口文件 ]

// 绑定当前访问到index模块

define('BIND_MODULE','web');

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

define('RUNTIME_PATH',__DIR__ . '/../runtime/');

require_once APP_PATH.'common_entry.php';