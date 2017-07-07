<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-07
 * Time: 13:49
 */

namespace app\task\command;


use app\src\crawler\action\JavformeRssAction;
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
}