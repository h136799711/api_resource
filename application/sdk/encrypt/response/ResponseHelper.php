<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-29
 * Time: 10:53
 */
namespace  app\sdk\encrypt\response;

use app\sdk\encrypt\algorithm\AlgParams;
use app\sdk\encrypt\algorithm\IAlgorithm;
use app\sdk\exception\ByInvalidParamException;
use app\sdk\helper\ByLangHelper;

class ResponseHelper {

    public static function getResponseParams($algInstance,$data,$client_id,$client_secret,$notify_id){

        if(!($algInstance instanceof IAlgorithm)){
            throw  new ByInvalidParamException(ByLangHelper::get('by_alg_instance_invalid'));
        }

        $data['data'] = self::toStringData($data['data']);

        self::checkNullData($data['data']);

        $type = ($data['code'] == 0) ? "T":"F";
        $data = $algInstance->encryptData($data);
        $algParams = new AlgParams();
        $algParams->setClientId($client_id);
        $algParams->setClientSecret($client_secret);
        $algParams->setNotifyId($notify_id);
        $algParams->setData($data);
        $algParams->setTime(strval(time()));
        $algParams->setType($type);

        return $algParams->getResponseParams();
    }


    /**
     * 返回数据做 转字符串处理
     * @param $data
     * @return array|string
     */
    protected static function toStringData($data){
        if(is_array($data)){
            foreach ($data as $key=>&$value){
                $data[$key] = self::toStringData($value);
            }
        }elseif(!is_object($data) && !is_string($data)){
            return strval($data);
        }

        return $data;
    }

    /**
     * 检查返回是否有null的数据
     * @param $data
     * @throws Exception
     */
    protected static function checkNullData($data){
        if(is_null($data)){
            throw new ByInvalidParamException('return data can\'t contain null data');
        }elseif(is_array($data)){
            foreach ($data as $value){
                self::checkNullData($value);
            }
        }elseif(is_object($data) && method_exists($data,"toArray")){
            foreach ($data->toArray() as $key=>$value){
                self::checkNullData($value);
            }
        }
    }


}