<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-14
 * Time: 10:38
 */

namespace app\sdk\constants;

/**
 * Class ByRetCode
 * 返回code值
 * @package app\sdk\constants
 */
class ByRetCode
{
    const RetCodeDes = [
        "1001" => '缺少参数'
    ];

    const FAILED = -1;

    // 1000 - 9999
    // api 接口返回 code
    /**
     * 缺少参数
     */
    const Api_Lack_Parameter = 1001;

    /**
     * 404请求资源不存在
     */
    const Api_Not_Found = 1002;

    /**
     * 无效\非法参数
     */
    const Api_Invalid_Parameter = 1003;

    /**
     * 业务错误
     */
    const Api_Business_Error = 1004;

    /**
     * 接口需要升级
     */
    const Api_Need_Upgrade = 1005;

    /**
     * 发生异常
     */
    const Api_EXCEPTION = 1006;


    /**
     * 接口无权限
     */
    const Api_No_Auth = 1007;


    /**
     * 需要登录
     */
    const Api_Need_Login = 1111;

    /**
     * 接口维护中
     */
    const Api_Under_Maintenance = 1112;

    /**
     * 接口停用
     */
    const Api_Disable = 1113;

    /**
     * 该接口版本不建议使用，请用最新接口
     */
    const Api_Service_Is_Deprecated = 9666;


}