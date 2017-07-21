<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-21
 * Time: 17:45
 */

namespace app\app\helper;


use app\src\base\helper\ConfigHelper;

class AppConfigHelper extends ConfigHelper
{
    /**
     * 判断是否根管理员
     * @param $uid
     * @return bool
     */
    public static function isRoot($uid){
        $admin = self::getValue('user_administrator');
        if(is_array($admin)){
            var_dump($uid);
            var_dump($admin);
            return in_array(intval($uid),$admin);
        }
        return $uid > 0 && $admin == intval($uid);
    }
}