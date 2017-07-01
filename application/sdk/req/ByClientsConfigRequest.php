<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-01
 * Time: 09:23
 */

namespace app\sdk\req;


use app\sdk\base\ByBaseSdkObj;

class ByClientsConfigRequest extends ByBaseSdkObj
{
    public function create($post){

        $data = [
            'type'=>'By_ClientsConfig_create',
            'api_ver'=>'100',
            'notify_id'=>self::getNotifyId(),
        ];

        $data = $this->merge($data,$post);
        var_dump($data);

        return $this->callRemote($data);
    }

    public function query(){

    }
}