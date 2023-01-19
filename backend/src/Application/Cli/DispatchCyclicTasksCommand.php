<?php

namespace App\Application\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

class DispatchCyclicTasksCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('app:dispatch:all')
            ->setDescription('Dispatches all tasks that should be run periodically.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getApplication()->setAutoExit(false);
        $minute = intval(date('H')) * 60 + intval(date('i'));
//        if ($minute % 2 == 0) {
//            $this->getApplication()->run(new StringInput('app:oauth:refresh-tokens'), $output);
//        }
//        if ($minute % 60 === 0) {
//            $this->getApplication()->run(new StringInput('clear:invalid-user-tokens'), $output);
//        }
        return 0;
    }
}
