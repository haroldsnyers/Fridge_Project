<?php


namespace App\Controller\API;


use App\Entity\Fridge;
use App\Form\FridgeType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/fridge")
 */
class FridgeControllerApi extends AbstractController
{
    /**
     * @Route("/list", name="api_fridgeRepo_show", methods={"GET"})
     */
    public function index(Request $request, UserRepository $userRepository)
    {
        $user = $request->query->get('email');
        try {
            $fridgeList = [];
            $listFridge = $userRepository->findOneByEmail($user)->getListFridges();
            foreach($listFridge as $fridge) {
                array_push($fridgeList, $fridge);
            }

            $encoders = array( new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $jsonContent = $serializer->serialize($listFridge,'json', [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]);
            $response = new JsonResponse();
            $response->setContent($jsonContent);

            return $response;

        } catch (\Exception $exception) {
            return $this->json([
                'errors' => $exception
            ], 400);
        }

    }

    /**
     * @Route("/create", name="create_product")
     */
    public function newFridge(Request $request)
    {
        // creates a task object and initializes some data for this example
        $fridge = new Fridge();
        $user = $this->getUser();

        $form = $this->createForm(FridgeType::class, $fridge);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump('submitted');
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $fridge = $form->getData();
            $fridge->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($fridge);
            $entityManager->flush();

            return $this->redirectToRoute('fridgeRepo_show');
        }

        return $this->render('fridge/CreateFridge.html.twig', [
            'fridgeForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="fridge_edit", methods={"GET","POST"})
     */
    public function editFridge(Request $request, Fridge $fridge): Response
    {
        $user = $this->getUser()->getUsername();
        $fridgeUser = $fridge->getUser()->getUsername();

        if ($user == $fridgeUser) {
            $form = $this->createForm(FridgeType::class, $fridge);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('fridgeRepo_show');
            }

            return $this->render('fridge/EditFridge.html.twig', [
                'fridge' => $fridge,
                'fridgeForm' => $form->createView(),
            ]);

        } else {
            return $this->render('home/homepage.html.twig');
        }
    }

    /**
     * @Route("/delete/{id}", name="delete_fridge", methods={"DELETE"})
     */
    public function deleteFridge(Request $request, Fridge $fridge) : Response
    {
        $user = $this->getUser()->getUsername();
        $fridgeUser = $fridge->getUser()->getUsername();

        if ($user == $fridgeUser) {
            if ($this->isCsrfTokenValid('delete'.$fridge->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($fridge);
                $entityManager->flush();
            }
            return $this->json([
                'message' => 'successful'
            ], 200);

        } else {
            return $this->render('home/homepage.html.twig');
        }
    }
}