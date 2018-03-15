<?php

namespace AppBundle\Command;

use AppBundle\Entity\AccessToken;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Unirest\Request;

class ObtainAccessTokenCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:obtain-access-token')
            ->setDescription('Obtain WeChat Access Token')
            ->setHelp('This command allows you to obtain WeChat Access Token');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $accessTokenManager = $this->getContainer()->get('app.access_token_manager');

        $output->writeln([
            '<info>Update WeChat Access Token</info>',
            '<info>=========Start=========</info>',
        ]);

        $latestAccessToken = $accessTokenManager->getLatestAccessToken();

        $output->writeln([
            'Current Access Token Create Time ' . $latestAccessToken->getCreatedAt()->format('Y-m-d H:i:s'),
            'Current Access Token Expire Time ' . $latestAccessToken->getExpiresIn() . 's',
            '<info>=========End==========</info>',
        ]);
    }
}