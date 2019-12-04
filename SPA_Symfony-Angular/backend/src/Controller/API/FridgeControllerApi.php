<?php


namespace App\Controller\API;


use App\Entity\Fridge;
use App\Form\FridgeType;
use App\Repository\FridgeRepository;
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
     * @Route("/", name="api_fridgeRepo_show", methods={"GET"})
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

            $defaultContext = [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context){
                    return $object->getId();
                },
                ObjectNormalizer::CIRCULAR_REFERENCE_LIMIT =>0,
                AbstractNormalizer::IGNORED_ATTRIBUTES =>['fridge', 'password', 'plainPassword', 'salt'],
                ObjectNormalizer::ENABLE_MAX_DEPTH => true,
            ];

            $encoders = array( new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $jsonContent = $serializer->serialize($listFridge,'json', $defaultContext);
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
     * @Route("/", name="create_fridge", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function newFridge(Request $request, UserRepository $userRepository)
    {
        // creates a task object and initializes some data for this example
        $fridge = new Fridge();

        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $type = $data['type'];
        $nbrFloors = $data['nbrFloors'];
        $userEmail = $data['userMail'];

        $user = $userRepository->findOneByEmail($userEmail);

        $fridge->setName($name);
        $fridge->setType($type);
        $fridge->setNbrFloors($nbrFloors);
        $fridge->setUser($user);


        try
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fridge);
            $entityManager->flush();
            return $this->json([
                'message' => "fridge Created!"
            ]);
        }
        catch(\Exception $e)
        {
            return $this->json([
                'errors' => "Unable to save new user at this time."
            ], 400);
        }
    }

    /**
     * @Route("/{id}", name="fridge_edit", methods={"PUT"})
     */
    public function editFridge(Request $request, FridgeRepository $fridgeRepository, Fridge $fridge): Response
    {
        $id_fridge = $request->attributes->get('id');
        $fridge = $fridgeRepository->findOneById($id_fridge);

        $data = json_decode($request->getContent(), true);

        $name = $data['name'];
        $type = $data['type'];
        $nbrFloors = $data['nbrFloors'];

        $fridge->setName($name);
        $fridge->setType($type);
        $fridge->setNbrFloors($nbrFloors);
        try {
            $this->getDoctrine()->getManager()->flush();

            return $this->json([
                'message' => "fridge Updated!"
            ]);

        } catch (\Exception $exception) {
            return $this->json([
                'errors' => $exception
            ], 400);
        }
    }

    /**
     * @Route("/{id}", name="delete_fridge", methods={"DELETE"})
     */
    public function deleteFridge(Request $request, FridgeRepository $fridgeRepository) : Response
    {
        $id_fridge = $request->attributes->get('id');
        $fridge = $fridgeRepository->findOneById($id_fridge);

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fridge);
            $entityManager->flush();

            return $this->json([
                'message' => 'Deletion successful'
            ], 200);

        } catch (\Exception $exception) {
            return $this->json([
                'errors' => $exception
            ], 400);
        }
    }
}