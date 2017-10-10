<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetAccessTokenCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:get-access-token')
            ->setDescription('Get WeChat Access Token')
            ->setHelp('This command allows you to get WeChat Access Token');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Get WeChat Access Token',
            '=======================',
            'Start'
        ]);

        $appId = $this->getContainer()->getParameter('app_id');
        $appSecret = $this->getContainer()->getParameter('app_secret');
        
        $output->writeln([
            '=======================',
            'End'
        ]);
    }
}