<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-14
 * Time: 10:31
 */

namespace app\sdk\helper;

use app\sdk\constants\ByRetCode;

/**
 * Class ByResultHelper
 * 返回结果都以这种格式
 * @package app\sdk\helper
 */
class ByResultHelper
{
    /**
     * 操作成功 code = 0
     * @param $data
     * @param string $info
     * @return array ['code','msg','data']
     */
    public static function success($data,$info=''){
        return ['code'=>0,'msg'=>$info,'data'=>$data];
    }

    /**
     *
     * 操作失败 code != 0
     * 具体code 值参考 ByRetCode
     * @param string $info
     * @param int $code
     * @return array ['code','msg','data']
     */
    public static function fail($info='',$code = ByRetCode::FAILED,$data=[]){
        return ['code' => $code,'msg' => $info,'data'=>$data];
    }
}