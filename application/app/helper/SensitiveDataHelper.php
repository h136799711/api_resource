<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-24
 * Time: 16:15
 */

namespace app\app\helper;

/**
 * Class SensitiveDataHelper
 * 敏感数据帮助类
 * @package app\app\helper
 */
class SensitiveDataHelper
{
    /**
     * 隐藏手机号
     * @param $mobile
     * @return string
     */
    public static function hiddenMobile($mobile){
        $len = strlen($mobile);
        if($len <= 6){
            return $mobile;
        }
        $str = substr($mobile,0,3).'****'.substr($mobile,$len-3,3);
        return $str;
    }
}