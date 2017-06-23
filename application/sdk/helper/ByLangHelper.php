<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-14
 * Time: 11:04
 */

namespace app\sdk\helper;


class ByLangHelper
{
    // 语言数据
    private static $lang = [];
    // 语言作用域
    private static $range = 'zh-cn';

    /**
     *
     * @param string $lang 语言
     */
    public static function setLang($lang='zh-cn'){
        self::range($lang);
        self::load(BY_SDK_PATH.BY_DS.'lang'.BY_DS.$lang.BY_EXT);

    }

    // 设定当前的语言
    public static function range($range = '')
    {
        if ('' == $range) {
            return self::$range;
        } else {
            self::$range = $range;
        }
        return self::$range;
    }

    /**
     * 获取语言定义(不区分大小写)
     * @param string|null   $name 语言变量
     * @param string        $range 语言作用域
     * @return mixed
     */
    public static function has($name, $range = '')
    {
        $range = $range ?: self::$range;
        return isset(self::$lang[$range][strtolower($name)]);
    }

    public static function load($file, $range = ''){
        $range = $range ?: self::$range;
        if (!isset(self::$lang[$range])) {
            self::$lang[$range] = [];
        }
        // 批量定义
        if (is_string($file)) {
            $file = [$file];
        }
        $lang = [];
        foreach ($file as $_file) {
            if (is_file($_file)) {
                $_lang = include $_file;
                if (is_array($_lang)) {
                    $lang = array_change_key_case($_lang) + $lang;
                }
            }
        }
        if (!empty($lang)) {
            self::$lang[$range] = $lang + self::$lang[$range];
        }
        return self::$lang[$range];
    }

    /**
     * 设置语言定义(不区分大小写)
     * @param string|array  $name 语言变量
     * @param string        $value 语言值
     * @param string        $range 语言作用域
     * @return mixed
     */
    public static function set($name, $value = null, $range = '')
    {
        $range = $range ?: self::$range;
        // 批量定义
        if (!isset(self::$lang[$range])) {
            self::$lang[$range] = [];
        }
        if (is_array($name)) {
            return self::$lang[$range] = array_change_key_case($name) + self::$lang[$range];
        } else {
            return self::$lang[$range][strtolower($name)] = $value;
        }
    }

    /**
     * 获取语言定义(不区分大小写)
     * @param string|null   $name 语言变量
     * @param array         $vars 变量替换
     * @param string        $range 语言作用域
     * @return mixed
     */
    public static function get($name = null, $vars = [], $range = '')
    {
        $range = $range ?: self::$range;
        // 空参数返回所有定义
        if (empty($name)) {
            return self::$lang[$range];
        }
        $key   = strtolower($name);
        $value = isset(self::$lang[$range][$key]) ? self::$lang[$range][$key] : $name;

        // 变量解析
        if (!empty($vars) && is_array($vars)) {
            /**
             * Notes:
             * 为了检测的方便，数字索引的判断仅仅是参数数组的第一个元素的key为数字0
             * 数字索引采用的是系统的 sprintf 函数替换，用法请参考 sprintf 函数
             */
            if (key($vars) === 0) {
                // 数字索引解析
                array_unshift($vars, $value);
                $value = call_user_func_array('sprintf', $vars);
            } else {
                // 关联索引解析
                $replace = array_keys($vars);
                foreach ($replace as &$v) {
                    $v = "{:{$v}}";
                }
                $value = str_replace($replace, $vars, $value);
            }

        }
        return $value;
    }

}