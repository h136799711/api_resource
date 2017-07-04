<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-04
 * Time: 16:43
 */

namespace app\task\command;


use app\src\baidu_api\fanyi\BDTranslateLangType;
use app\src\baidu_api\fanyi\BDTranslater;
use app\src\base\helper\PageHelper;
use app\src\qqav\action\LogAction;
use app\src\qqav\logic\VideoTagsLogic;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Translate extends Command
{
    private $appId = "20170703000062287";
    private $appSecret = "sgel8u4tNsl42t_pC9j2";

    protected function configure()
    {
        $this->setName('translate')->setDescription('translate tags to chinese');
    }

    protected function execute(Input $input, Output $output)
    {
        $start = time();
        $output->writeln(date("Y-m-d H:i:s",$start)." start");
        $order = "id asc";
        $map = ['tag_cn'=>''];
        $page = ['page_index'=>0,'page_size'=>500];
        $result = (new VideoTagsLogic())->query($map,PageHelper::renew($page)->queryParam(),$order);
        $info = $result['info'];
        if(array_key_exists("list",$info) && count($info['list']) > 0){
            $translate = (new BDTranslater($this->appId,$this->appSecret));
            foreach ($info['list'] as $tag){
                $id = $tag['id'];
                $src = $tag['tag_en'];
                $result = $translate->translate($src,BDTranslateLangType::En,BDTranslateLangType::Zh);
                if(array_key_exists("trans_result",$result)){
                    $trans_result = $result['trans_result'];
                    $tag_cn = $trans_result[0]['dst'];
                    (new VideoTagsLogic())->save(['id'=>$id],['tag_cn'=>$tag_cn]);
                }elseif(array_key_exists("error_code",$result)){
                    LogAction::debug($result['error_msg']);
                }
            }
        }
        $elapse = time() - $start;
        $desc = ( $elapse / 60 ).'m,'.($elapse % 60).'s';
        $output->writeln(" 耗时 ".$desc);
    }
}