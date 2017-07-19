<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-19
 * Time: 11:48
 */

namespace app\index\model;


use think\Model;

class Test extends Model
{
    protected  $table = "shop_test";
    // 设置当前模型的数据库连接
    protected $connection = [
        // 数据库类型
        'type'        => 'mysql',
        // 服务器地址
        'hostname'    => '127.0.0.1',
        // 数据库名
        'database'    => 'shopstore',
        // 用户名
        'username'       => 'hebidu',
        // 密码
        'password'       => ',136799711hbdHBD',
        // 数据库编码默认采用utf8
        'charset'     => 'utf8',
        // 数据库表前缀
        'prefix'      => 'think_',
        // 数据库调试模式
        'debug'       => true,
    ];
}