<?php
namespace  app\task\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-04
 * Time: 16:14
 */
class Test extends Command
{
    protected function configure()
    {
        $this->setName('test')->setDescription('Here is the remark ');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln("TestCommand:");
    }
}