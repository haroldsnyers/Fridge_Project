<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/user")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="api_login", methods={"POST"})
     */
    public function login()
    {
        return $this->json(['result' => true], 200);
    }

    /**
     * @Route("/logout", name="api_logout")
     */
    public function logout(){

    }
}
