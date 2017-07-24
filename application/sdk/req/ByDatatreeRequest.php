<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-24
 * Time: 16:35
 */

namespace app\sdk\req;


use app\sdk\base\ByBaseSdkObj;
use app\src\base\helper\PageHelper;

class ByDatatreeRequest extends ByBaseSdkObj
{
    public function query($map,PageHelper $pageHelper,$order=false,$fields=false){
        $data = [
            'type'=>'By_Datatree_query',
            'api_ver'=>'100',
            'notify_id'=>self::getNotifyId(),
        ];
        $data['page_index'] = $pageHelper->getPageIndex();
        $data['page_size'] = $pageHelper->getPageSize();
        foreach ($map as $key=>$value){
            $data[$key] = $value;
        }

        if(!empty($order)){
            $data['order'] = $order;
        }
        if(!empty($fields)){
            $data['fields'] = $fields;
        }

        return $this->callRemote($data);
    }
}