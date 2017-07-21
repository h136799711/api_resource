<?php
// [ 应用入口文件 ]

// 绑定当前访问到index模块

define('BIND_MODULE','pc');

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

// 定义运行时目录
define('RUNTIME_PATH',__DIR__ . '/../runtime/');

require_once APP_PATH.'common_entry.php';