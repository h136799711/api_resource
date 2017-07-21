<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All ights reserved
 */

// [ 应用入口文件 ]
//UTC: Etc/GMT
//东八区：Asia/Shanghai

$self = strip_tags($_SERVER['REQUEST_URI']);
if(strlen($self) > 9 && strpos($self,"admin.php") == strlen($self) - 9){
    header("Location: $self/admin/index/login");
}
// URL常量
define('__SELF__',strip_tags($_SERVER['REQUEST_URI']));
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
// 定义运行时目录
define('RUNTIME_PATH',__DIR__ . '/../runtime/');

require_once APP_PATH.'common_entry.php';