<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/register", name="api_user_registration", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $username = $data['username'];
        $password = $data['password'];
        $passwordConfirmation = $data['passwordConfirmation'];
        $errors = [];
        if($password != $passwordConfirmation)
        {
            array_push($errors, "Password does not match the password confirmation.");
            # $errors[] = "Password does not match the password confirmation.";
        }
        if(strlen($password) < 8)
        {
            array_push($errors, "Password should be at least 8 characters.");
            # $errors[] = "Password should be at least 8 characters.";
        }
        if(!$errors)
        {
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            $user->setEmail($email);
            $user->setUsername($username);
            $user->setPassword($encodedPassword);
            try
            {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->json([
                    'message' => "succesfuly registered"
                ], 200);
            }
            catch(UniqueConstraintViolationException $e)
            {
                array_push($errors, "The email or user provided already has an account!");
                # $errors[] = "The email or user provided already has an account!";
            }
            catch(\Exception $e)
            {
                $errors[] = "Unable to save new user at this time.";
            }
        }
        return $this->json([
            'errors' => $errors
        ], 400);


    }
}
