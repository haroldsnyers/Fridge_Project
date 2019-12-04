<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class HomeControllerApi extends AbstractController
{
    /**
     * @Route("/home", name="app_homepage")
     */
    public function homePage()
    {
        return $this->json('hello');
    }
}
