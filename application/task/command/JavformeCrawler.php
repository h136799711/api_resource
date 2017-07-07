<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-04
 * Time: 17:35
 */

namespace app\task\command;


use app\src\crawler\action\JavformeCrawlerAction;
use app\src\crawler\logic\CrawlerUrlLogic;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class JavformeCrawler extends Command
{
    protected function configure()
    {
        $this->setName('crawler:javforme')
            ->addOption('size','',Option::VALUE_OPTIONAL,'crawler size',10)
            ->setDescription('javfor.me site crawler');

    }

    protected function execute(Input $input, Output $output)
    {
        $start = time();
        $output->writeln('\n'.date("Y-m-d H:i:s",$start)." start");

        $size = $input->getOption('size');
        $this->start($size);
        $elapse = time() - $start;
        $desc = intval(floor( $elapse / 60 )).'m,'.($elapse % 60).'s';
        $output->writeln("\n cost ".$desc);
    }

    protected function start($size=10){
        $order = "update_time asc";
        $map = ['climb_status'=>0];
        $page = ['curpage'=>0,'size'=>$size];
        $result = (new CrawlerUrlLogic())->query($map,$page,$order);
        $info = $result['info'];
        if(array_key_exists("list",$info) && count($info['list']) > 0){
            $crawler = new JavformeCrawlerAction();
            foreach ($info['list'] as $urlItem){
                $url = $urlItem['url'];
                $result = $crawler->parse($url);
                var_dump($result);
            }
            return 'success';
        }
        return 'fail';
    }
}