<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-19
 * Time: 20:29
 */

namespace app\app\helper;


use app\src\menu\logic\MenuLogic;
use app\src\powersystem\logic\AuthGroupAccessLogic;
use app\src\powersystem\logic\AuthGroupLogic;

class AppMenuHelper
{
    public static function getCurrentUserMenu($uid){

        $map = array('uid' => $uid);
        $result = (new AuthGroupAccessLogic())->queryNoPaging($map);
        $menuList = "";
        if ($result['status']) {
            $group_ids = '';
            foreach ($result['info'] as $groupAccess) {
                $group_ids .= $groupAccess['group_id'] . ',';
            }

            unset($map['uid']);
            if (!empty($group_ids)) {
                $map = array('id' => array('in', rtrim($group_ids, ",")));
                $result = (new AuthGroupLogic())->queryNoPaging($map);

                if ($result['status'] && is_array($result['info'])) {
                    foreach ($result['info'] as $group) {
                        //合并多角色
                        $menuList .= $group['menulist'];
                    }

                }
            } else {

            }
        }
        return $menuList;
    }


    /**
     *
     * @param  $uid
     * @return array|bool|mixed
     */
    public static function getMenu($uid)
    {

        $map = [];
        $result = (new MenuLogic())->queryNoPaging($map, "sort desc");

        $menuList = [];
        if (!$result['status']) {
            echo($result['info']);
        } else {    
            $list = $result['info'];
            $current_menus = AppMenuHelper::getCurrentUserMenu($uid);
            $current_menus = explode(',', rtrim($current_menus, ','));

            $current_menus = array_unique($current_menus);
            foreach ($list as $val) {
                if (in_array($val['id'], $current_menus) || AppConfigHelper::isRoot($uid)) {
                    $menuList[] = [
                        'Id' => $val['id'],
                        'ParentId' => $val['pid'],
                        'Name' => $val['title'],
                        'Icon' => $val['icon'],
                        'UrlAddress' => $val['url'] != '#' ? $val['url'] : '#',
                        'IsFront' => $val['is_front']
                    ];
                }
            }

        }

        return $menuList;
    }


    /**
     * 将格式数组转换为树
     *
     * @param array $list
     * @param integer $level 进行递归时传递用的参数
     */
    private static $formatTree; //用于树型数组完成递归格式的全局变量

    private static function _toFormatTree($list, $level = 0, $title = 'title')
    {
        foreach ($list as $key => $val) {
            $tmp_str = str_repeat("&nbsp;", $level * 2);
            $tmp_str .= "└";

            $val['level'] = $level;
            $val['title_show'] = $level == 0 ? $val[$title] . "&nbsp;" : $tmp_str . $val[$title] . "&nbsp;";
            // $val['title_show'] = $val['id'].'|'.$level.'级|'.$val['title_show'];


            if (!array_key_exists('_child', $val)) {
                array_push(self::$formatTree, $val);
            } else {
                $tmp_ary = $val['_child'];
                unset($val['_child']);
                array_push(self::$formatTree, $val);
                self::_toFormatTree($tmp_ary, $level + 1, $title); //进行下一层递归
            }
        }
        return;
    }

    public static function toFormatTree($list, $title = 'title', $pk = 'id', $pid = 'pid', $root = 0)
    {
        $list = self::list_to_tree($list, $pk, $pid, '_child', $root);
        self::$formatTree = array();
        self::_toFormatTree($list, 0, $title);
        return self::$formatTree;
    }


    public static function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
    {
        // 创建Tree
        $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }
        return $tree;
    }
}