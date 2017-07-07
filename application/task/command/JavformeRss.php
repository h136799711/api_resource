<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-07
 * Time: 13:49
 */

namespace app\task\command;


use app\src\base\helper\ValidateHelper;
use app\src\crawler\action\JavformeRssAction;
use app\src\crawler\constants\CrawlerUrlType;
use app\src\crawler\logic\CrawlerUrlLogic;
use app\src\qqav\action\LogAction;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class JavformeRss extends Command
{
    protected function configure()
    {
        $this->setName('rss:javforme')
            ->setDescription('javfor.me site rss reader');

    }

    protected function execute(Input $input, Output $output)
    {
        $start = time();
        $output->writeln(date("Y-m-d H:i:s",$start)." start");
        $url = "http://feeds.feedburner.com/JavForMe?format=xml";
        $result = (new JavformeRssAction())->read($url);
        LogAction::logDebugResult($result);
        $elapse = time() - $start;
        $desc = intval(floor( $elapse / 60 )).'m,'.($elapse % 60).'s';
        $output->writeln(" cost ".$desc);
    }

    private function read($url){
        $xml = file_get_contents($url);
        $xml = simplexml_load_string($xml);
        $items = $xml->xpath("channel/item");
        $now = time();
        $allEntity = [];
        foreach ($items as $item){
            $url = ''.$item->link;
            $map = ['url'=>$url];
            $result = (new CrawlerUrlLogic())->getInfo($map);

            if(ValidateHelper::legalArrayResult($result) && $result['info']['url'] == $url) {
                //已经存在则不添加了
                continue;
            }
            array_push($allEntity,[
                'url'=>$url,
                'create_time'=>$now,
                'update_time'=>$now,
                'climb_status'=>0,
                'url_type'=>CrawlerUrlType::JAV_FOR_ME,
            ]);
        }
        if(count($allEntity) > 0){
            $result = (new CrawlerUrlLogic())->addAll($allEntity);
        }
    }
}