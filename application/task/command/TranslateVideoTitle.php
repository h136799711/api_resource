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
use app\src\qqav\logic\VideoLogic;
use app\src\qqav\logic\VideoTagsLogic;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class TranslateVideoTitle extends Command
{
    private $appId = "20170703000062287";
    private $appSecret = "sgel8u4tNsl42t_pC9j2";

    protected function configure()
    {
        $this->setName('translate:video_title')
            ->addOption('size','',Option::VALUE_OPTIONAL,'translate size',10)
            ->setDescription('translate video title to chinese');

    }

    protected function execute(Input $input, Output $output)
    {
        $start = time();
        $output->writeln(date("Y-m-d H:i:s",$start)." start");

        $size = $input->getOption('size');
        $this->video_title($size);
        $elapse = time() - $start;
        $desc = ( $elapse / 60 ).'m,'.($elapse % 60).'s';
        $output->writeln(" cost ".$desc);
    }

    protected function video_title($size){
        $order = "id asc";
        $map = ['title_cn'=>''];
        $page = ['page_index'=>0,'page_size'=>$size];
        $result = (new VideoLogic())->query($map,PageHelper::renew($page)->queryParam(),$order);
        $info = $result['info'];
        if(array_key_exists("list",$info) && count($info['list']) > 0){
            $translate = (new BDTranslater($this->appId,$this->appSecret));
            foreach ($info['list'] as $tag){
                $id = $tag['id'];
                $src = $tag['title_en'];
                $result = $translate->translate($src,BDTranslateLangType::En,BDTranslateLangType::Zh);
                if(array_key_exists("trans_result",$result)){
                    $trans_result = $result['trans_result'];
                    $title_cn = $trans_result[0]['dst'];
                    (new VideoLogic())->save(['id'=>$id],['title_cn'=>$title_cn]);
                }elseif(array_key_exists("error_code",$result)){
                    LogAction::debug($result['error_msg']);
                }
            }
        }
    }
}