<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-03
 * Time: 16:57
 */

namespace app\task\controller;


use app\src\base\helper\PageHelper;
use app\src\crawler\action\RemotePictureHelper;
use app\src\qqav\action\LogAction;
use app\src\qqav\action\VideoAction;
use app\src\qqav\logic\VideoLogic;

class VideoPic
{
    public function remote2local(){
        $order = "update_time asc";
        $map = ['local_main_image'=>0];
        $page = ['page_index'=>0,'page_size'=>2];
        $result = (new VideoAction())->query($map,PageHelper::renew($page),$order);
        $info = $result['info'];
        if(array_key_exists("list",$info) && count($info['list']) > 0){
            foreach ($info['list'] as $urlItem){
                $url = $urlItem['main_image'];
                $result = RemotePictureHelper::download($url);
                LogAction::logDebugResult($result);
                if($result['status']){
                    $id = intval($result['info']);
                    $map = ['id'=>$urlItem['id']];
                    $result = (new VideoLogic())->save($map,['local_main_image'=>$id]);
                    LogAction::logDebugResult($result);
                }
            }
            return 'success';
        }
        return  'fail';
    }
}