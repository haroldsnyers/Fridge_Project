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
class UserControllerApi extends AbstractController
{
    /**
     * @Route("/register", name="api_user_registration", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
//        try {
//            $data = json_decode($request->getContent(), true);
//
//            // 1) build the form
//            $user = new User();
//            $form = $this->createForm(UserType::class, $user);
//
//            // 2) handle the submit (will only happen on POST)
//            $form->submit($data);
//
//            // 3) Encode the password (you could also do this via Doctrine listener)
//            $password = $passwordEncoder->encodePassword($user, 'engage');
//            $user->setPassword($password);
//
//            // 4) save the User!
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->persist($user);
//            $entityManager->flush();
//
//            // ... do any other work - like sending them an email, etc
//            // maybe set a "flash" success message for the user
//
//
//            return new JsonResponse("Registered", 201);
//
//        } catch (\Exception $exception) {
//
//            return new JsonResponse([
//                'success' => false,
//                'code' => $exception->getCode(),
//                'message' => $exception->getMessage()
//            ]);
//        }

        $user = new User();
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $username = $data['username'];
        $password = $data['password'];
        $passwordConfirmation = $data['passwordConfirmation'];
        $errors = [];
        if($password != $passwordConfirmation)
        {
            $errors[] = "Password does not match the password confirmation.";
        }
        if(strlen($password) < 2)
        {
            $errors[] = "Password should be at least 6 characters.";
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
                    'user' => $user
                ]);
            }
            catch(UniqueConstraintViolationException $e)
            {
                $errors[] = "The email or user provided already has an account!";
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
