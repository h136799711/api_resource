<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-06
 * Time: 16:14
 */

namespace app\src\aliyun_api;


class AreaData
{
    public function demo(){
        $host = "http://jisuarea.market.alicloudapi.com";
        $path = "/area/all";
        $method = "GET";
        $appcode = "d9683b82762043a4a00daa592b6f62f6";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $querys = "";
        $bodys = "";
        $url = $host . $path;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }
}