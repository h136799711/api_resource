<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-03
 * Time: 18:19
 */

namespace app\src\baidu_api\fanyi;


define("BD_FY_CURL_TIMEOUT",   10);
define("BD_FY_URL",            "http://api.fanyi.baidu.com/api/trans/vip/translate");

class BDTranslater
{

    protected $app_id;
    protected $sec_key;

    public function __construct($appId,$sec_key){
        $this->app_id = $appId;
        $this->sec_key = $sec_key;
    }

    //翻译入口
    public  function translate($query, $from, $to)
    {
        $args = array(
            'q' => $query,
            'appid' => $this->app_id,
            'salt' => rand(10000,99999),
            'from' => $from,
            'to' => $to,

        );
        $args['sign'] = $this->buildSign($query, $this->app_id, $args['salt'], $this->sec_key);
        $ret = $this->call(BD_FY_URL, $args);
        $ret = json_decode($ret, true);
        return $ret;
    }

    //加密
    protected function buildSign($query, $appID, $salt, $secKey)
    {
        $str = $appID . $query . $salt . $secKey;
        $ret = md5($str);
        return $ret;
    }

    /**
     * 发起网络请求
     * @param $url
     * @param null $args
     * @param string $method
     * @param int $testflag
     * @param int $timeout
     * @param array $headers
     * @return bool|mixed
     */
    protected function call($url, $args=null, $method="post", $testflag = 0, $timeout = BD_FY_CURL_TIMEOUT, $headers=array())
    {/*{{{*/
        $ret = false;
        $i = 0;
        while($ret === false)
        {
            if($i > 1)
                break;
            if($i > 0)
            {
                sleep(1);
            }
            $ret = $this->callOnce($url, $args, $method, false, $timeout, $headers);
            $i++;
        }
        return $ret;
    }/*}}}*/

    function callOnce($url, $args=null, $method="post", $withCookie = false, $timeout = BD_FY_CURL_TIMEOUT, $headers=array())
    {
        $ch = curl_init();
        if($method == "post")
        {
            $data = $this->convert($args);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        else
        {
            $data = convert($args);
            if($data)
            {
                if(stripos($url, "?") > 0)
                {
                    $url .= "&$data";
                }
                else
                {
                    $url .= "?$data";
                }
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(!empty($headers))
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if($withCookie)
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
        }
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    function convert(&$args)
    {
        $data = '';
        if (is_array($args))
        {
            foreach ($args as $key=>$val)
            {
                if (is_array($val))
                {
                    foreach ($val as $k=>$v)
                    {
                        $data .= $key.'['.$k.']='.rawurlencode($v).'&';
                    }
                }
                else
                {
                    $data .="$key=".rawurlencode($val)."&";
                }
            }
            return trim($data, "&");
        }
        return $args;
    }
}