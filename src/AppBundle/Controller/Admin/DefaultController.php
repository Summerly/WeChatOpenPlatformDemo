<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Manager\AccessTokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="admin_index")
     */
    public function indexAction(AccessTokenManager $accessTokenManager)
    {
        $latestAccessToken = $accessTokenManager->getLatestAccessToken();

        return $this->render('admin/default/index.html.twig');
    }
}