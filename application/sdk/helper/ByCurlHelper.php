<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-14
 * Time: 10:18
 */

namespace app\sdk\helper;


use app\sdk\encrypt\algorithm\AlgFactory;
use app\sdk\encrypt\algorithm\AlgParams;
use app\sdk\exception\ByInvalidParamException;
use app\sdk\exception\ByLackParamException;

class ByCurlHelper
{
    private $gatewayUri;// 接口网关地址
    private $client_id;
    private $client_secret;
    private $algInstance;// 通信算法
    private $alg;
    private $debug = true;

    private static $helper = false;

    function __construct($cfg = array()){
        if(!isset($cfg['by_api_gateway_uri']) || !isset($cfg['by_client_id'])
            || !isset($cfg['by_client_secret']) ){
            throw  new ByLackParamException(ByLangHelper::get('by_exception_miss_api_param'));
        }

        $this->gatewayUri        = rtrim($cfg['by_api_gateway_uri'],'/');
        $this->client_id     = $cfg['by_client_id'];
        $this->client_secret = $cfg['by_client_secret'];
        $this->alg           = $cfg['by_alg'];
        $this->algInstance   = AlgFactory::getAlg($this->alg);
        $this->debug         = isset($cfg['by_api_debug']) ? $cfg['by_api_debug'] : false;
        if(empty($this->algInstance)){
            throw  new ByInvalidParamException(ByLangHelper::get('by_exception_invalid_alg',['alg'=>$this->alg]));
        }

    }

    /**
     *
     * @param $data
     * @param string $api_url
     * @return array
     * @internal param $url
     * @internal param bool $is_debug
     */
    public function callRemote($data,$api_url=''){
        if(empty($api_url)){
            $api_url = $this->gatewayUri;
        }
        $now = time();
        if(!isset($data['type']) || empty($data['type'])){
            return ByResultHelper::fail(ByLangHelper::get('by_param_need',['param'=>'type']));
        }

        if(!isset($data['api_ver']) || empty($data['api_ver'])){
            return ByResultHelper::fail(ByLangHelper::get('by_param_need',['param'=>'api_ver']));
        }

        $type = $data['type'];
        unset($data['type']);
        $apiVer = $data['api_ver'];
        unset($data['api_ver']);
        if(!isset($data['notify_id']) || empty($data['notify_id'])){
            $notify_id = $now;
        }else{
            $notify_id = $data['notify_id'];
            unset($data['notify_id']);
        }

        $encrypt_data = $this->algInstance->encryptData($data);
        $algParams = new AlgParams();
        $algParams->setClientId($this->client_id);
        $algParams->setClientSecret($this->client_secret);
        $algParams->setData($encrypt_data);
        $algParams->setNotifyId($notify_id);
        $algParams->setTime(strval($now));
        $algParams->setType($type);
        $algParams->setApiVer($apiVer);

        $itboye = $this->algInstance->encryptTransmissionData($algParams->getResponseParams(),$this->client_secret);
        $param = [
            'itboye'    => $itboye,
            'client_id' => $this->client_id,
            'alg'       => $this->alg
        ];
        $r = $this->curlPost($param,$api_url);
        // 同一进程 - 出错全部终止
        if($r['status']){
            //curl请求成功 - 不代表服务器执行结果
            $info = $r['info'];
            if(isset($info['data']) && isset($info['sign'])){

                $decrypt_data = $this->algInstance->decryptData($info['data']);
                $algParams = new AlgParams();
                $algParams->initFromArray($info);
                $algParams->setClientSecret($this->client_secret);

                // 签名校验
                if(!$this->algInstance->verify_sign($info['sign'],$algParams)){

                    $r = ByResultHelper::fail(ByLangHelper::get('by_alg_verify_sign_fail'));
                }
                if($decrypt_data['code'] != 0){
                    $pre = $this->debug ? 'API_'.$type.'=>' : '';
                    $r = ByResultHelper::fail($pre.$decrypt_data['data']);
                }else{
                    $r = ByResultHelper::success($decrypt_data['data'],ByLangHelper::get('by_success'));
                }

            }else{
                $r = ByResultHelper::fail($info);
            }

        }

        return $r;
    }

    /**
     * 取得accessToken
     * @return mixed
     */
    public function getAccessToken(){
        return $this->client_id;
    }


    /**
     *
     * @param $data  array
     * @param bool $api_url
     * @return array
     * @internal param $url
     */
    protected function curlPost($data, $gatewayUri=false) {

        if(!empty($gatewayUri)){
            $url = $gatewayUri;
        }else{
            $url = $this->gatewayUri;
        }
        $url = rtrim($url,"/");
        $ch     = curl_init();
        $header = ['Accept-Charset'=>"utf-8"];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.64 Safari/537.36'); //chrome46/mac
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        $error = curl_errno($ch);
        if($error){
            return ['status' => false, 'info' => $error];
        }else{

            $js = json_decode($tmpInfo,true);
            if(is_null($js)){
                $js = "$tmpInfo";
            }
            return ['status' => true, 'info' => $js];
        }
    }
}