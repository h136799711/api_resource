<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-10
 * Time: 17:21
 */
namespace  app\index\controller;

use app\src\email\action\EmailSendAction;
use app\src\jobs\Hello;
use app\src\jobs\JobsHelper;
use think\controller\Rest;

class  Queue extends Rest {
    public function test(){
        $content = '<html></html><p>中文yingwen English</p>';
        $data = [
            'to_email'=>'346551990@qq.com',
            'title'=>'123test_queue_email_task',
            'content'=>$content
        ];JobsHelper::pushEmailJob($data);
        echo "push";
        $result = (new EmailSendAction())->send($data['to_email'],'email-send-action'.$data['title'],$data['content']);
        echo "<br/>push";
        var_dump($result);
        echo "<br/>push";
    }
}