<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

// empty authenticator to log user via api
class LoginAuthenticator extends AbstractGuardAuthenticator
{
    // adding constructor to use dependency injections
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request)
    {
        // returns route where login is being used
        return $request->get("_route") === "api_login" && $request->isMethod("POST");
    }

    public function getCredentials(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        return [
            'email' => $data["email"],
            'password' => $data["password"]
        ];
    }

    // $credential = email and password returned from the getCredentials
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials['email']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    // method triggers if getUser method, loadUserByUsername can't find user with email given
    // also if credentials are not correct
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'error' => $exception->getMessageKey()
        ], 400);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new JsonResponse([
            'result' => true
        ]);
    }

    // method gets called whenever an endpoint is hit that requires authentication
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse([
            'error' => 'Access Denied'
        ]);
    }

    public function supportsRememberMe()
    {
        return false; // set to true if you want to use this method
    }
}
