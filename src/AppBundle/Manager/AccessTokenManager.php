<?php

namespace AppBundle\Manager;

use AppBundle\Entity\AccessToken;
use Doctrine\ORM\EntityManager;
use Unirest\Request;

class AccessTokenManager
{
    private $appId;
    private $appSecret;
    private $em;
    private $repository;

    public function __construct(string $appId, string $appSecret, EntityManager $em)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->em = $em;
        $this->repository = $em->getRepository(AccessToken::class);
    }

    public function getLatestAccessToken()
    {
        $latestAccessToken = $this->repository->getLatestAccessToken();

        if (!$latestAccessToken || $latestAccessToken->isExpired()) {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';

            $response = Request::get($url, [], [
                'grant_type' => 'client_credential',
                'appid'      => $this->appId,
                'secret'     => $this->appSecret,
            ]);

            if ($latestAccessToken && $latestAccessToken->isExpired()) {
                $this->em->remove($latestAccessToken);
            }

            $token = $response->body->access_token;
            $expiresIn = $response->body->expires_in;

            $newAccessToken = new AccessToken();
            $newAccessToken->setToken($token);
            $newAccessToken->setExpiresIn($expiresIn);

            $this->em->persist($newAccessToken);
            $this->em->flush();

            $latestAccessToken = $newAccessToken;
        }

        return $latestAccessToken;
    }
}