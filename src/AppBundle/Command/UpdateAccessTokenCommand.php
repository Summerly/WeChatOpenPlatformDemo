<?php

namespace AppBundle\Command;

use AppBundle\Entity\AccessToken;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Unirest\Request;

class UpdateAccessTokenCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:update-access-token')
            ->setDescription('Update WeChat Access Token')
            ->setHelp('This command allows you to update WeChat Access Token');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $appId = $this->getContainer()->getParameter('app_id');
        $appSecret = $this->getContainer()->getParameter('app_secret');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');


        $output->writeln([
            '<info>Update WeChat Access Token</info>',
            '<info>=========Start=========</info>',
        ]);

        $latestAccessToken = $em->getRepository(AccessToken::class)->getLatestAccessToken();

        if (!$latestAccessToken || $latestAccessToken->isExpired()) {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';

            $response = Request::get($url, [], [
                'grant_type' => 'client_credential',
                'appid'      => $appId,
                'secret'     => $appSecret
            ]);

            $token = $response->body->access_token;
            $expiresIn = $response->body->expires_in;

            $latestAccessToken = new AccessToken();
            $latestAccessToken->setToken($token);
            $latestAccessToken->setExpiresIn($expiresIn);

            $em->persist($latestAccessToken);
            $em->flush();
        }

        $output->writeln([
            'Current Access Token Create Time ' . $latestAccessToken->getCreatedAt()->format('Y-m-d H:i:s'),
            'Current Access Token Expire Time ' . $latestAccessToken->getExpiresIn() . 's',
            '<info>=========End==========</info>',
        ]);
    }
}