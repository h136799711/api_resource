<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-19
 * Time: 19:59
 */
return [
    'exception_handle'       => '\\app\\app\\exception\\AppExceptionHandler',
    'default_return_type'    => 'json',
    // 异常页面的模板文件
    'exception_tmpl'         => APP_PATH . 'index' . DS . 'view/exception.json',
    // 异常处理忽略的错误类型，支持PHP所有的错误级别常量，多个级别可以用|运算法
    // 参考：http://php.net/manual/en/errorfunc.constants.php
    'exception_ignore_type'  => 0,
    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 错误定向页面
    'error_page'             => '',
    // 显示错误信息
    'show_error_msg'         => false,
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