<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
namespace tests;

use app\src\clients\helper\RandomHelper;

class ExampleTest extends TestCase
{

    public function testBasicExample()
    {
        echo 'client_id';
        $str =  RandomHelper::getClientID(36);
        echo $str;
        assert(strlen($str) < 28,"length less than 28");
    }
}