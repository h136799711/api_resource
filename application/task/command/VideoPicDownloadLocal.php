<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-04
 * Time: 21:55
 */

namespace app\task\command;


use app\src\base\helper\PageHelper;
use app\src\crawler\action\RemotePictureHelper;
use app\src\qqav\action\LogAction;
use app\src\qqav\action\VideoAction;
use app\src\qqav\logic\VideoLogic;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class VideoPicDownloadLocal extends Command
{
    protected function configure()
    {
        $this->setName('video_pic:download2local')
            ->addOption('size','',Option::VALUE_OPTIONAL,'process size',10)
            ->setDescription('download video main image to local server');

    }

    protected function execute(Input $input, Output $output)
    {
        $start = time();
        $output->writeln(date("Y-m-d H:i:s",$start)." start");

        $size = $input->getOption('size');
        $this->remote2local($size);
        $elapse = time() - $start;
        $desc = intval(floor( $elapse / 60 )).'m,'.($elapse % 60).'s';
        $output->writeln(" cost ".$desc);
    }

    private function remote2local($size=50){
        $order = "update_time asc";
        $map = ['local_main_image'=>0];
        $page = ['page_index'=>0,'page_size'=>50];
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