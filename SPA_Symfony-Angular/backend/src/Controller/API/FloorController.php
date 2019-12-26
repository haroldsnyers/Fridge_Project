<?php

namespace App\Controller\API;

use App\Entity\Floor;
use App\Entity\Fridge;
use App\Form\FloorType;
use App\Repository\FloorRepository;
use App\Repository\FoodRepository;
use App\Repository\FridgeRepository;
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
 * @Route("/api/floors")
 */
class FloorController extends AbstractController
{
    /**
     * @Route("/", name="floor_index", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, FridgeRepository $fridgeRepository): Response
    {
        $id_fridge = $request->query->get('idFridge');

        try {
            $state = $this->generateFloorsFridge($id_fridge, $fridgeRepository);
            if ($state == 0) {
                return $this->json([
                    'errors' => "unable to fetch floors"
                ], 400);
            }
            $listFloors = $this->getFloorsFridge($id_fridge);

            $defaultContext = [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context){
                    return $object->getId();
                },
                ObjectNormalizer::CIRCULAR_REFERENCE_LIMIT =>0,
                AbstractNormalizer::IGNORED_ATTRIBUTES =>['fridge', 'user', 'floor', "nbrFloors", "imageFridgePath", "__initializer__", "__cloner__", "__isInitialized__"],
                ObjectNormalizer::ENABLE_MAX_DEPTH => true
            ];

            $encoders = array( new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $jsonContent = $serializer->serialize($listFloors,'json', $defaultContext);
            $response = new JsonResponse();
            $response->setContent($jsonContent);

            return $response;

        } catch (\Exception $exception) {
            return $this->json([
                'errors' => "unable to fetch floors"
            ], 400);
        }
    }

    public function getFloorsFridge ($id_fridge)
    {
        $floorList = [];

        $listFloors = $this->getDoctrine()
            ->getRepository(Floor::class)
            ->findFloorsFromFridge($id_fridge);
        foreach($listFloors as $floor) {
            array_push($floorList, $floor);
        }
        return $floorList;
    }

    public function generateFloorsFridge ($id_fridge, FridgeRepository $fridgeRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $floorList = $this->getFloorsFridge($id_fridge);

        $fridge = $fridgeRepository->findOneById($id_fridge);
        if (!$fridge) {
            return 0;
        }
        $nbr_floors = $fridge->getNbrFloors();
        $nbr_floors_real = count($floorList);
        $i = 0;
        if ($nbr_floors_real != $nbr_floors){
            $i = $nbr_floors - $nbr_floors_real;
        }

        while ($i != 0) {
            $floor = new Floor();
            $floor->setName('bottom floor');
            $floor->setType([]);
            $floor->setQtyFood(0);
            $floor->setIdFridge($fridge);
            $entityManager->persist($floor);
            $entityManager->flush();
            $i--;
        }
        return 1;
    }

    /**
     * @Route("/", name="floor_new", methods={"POST"})
     * @param Request $request
     */
    public function addFloor (Request $request, FridgeRepository $fridgeRepository)
    {
        try
        {
            $floor = new Floor();

            $data = json_decode($request->getContent(), true);
            $name = $data['name'];
            $type = $data['type'];
            $id_fridge = $data['id_fridge'];

            $fridge = $fridgeRepository->findOneById($id_fridge);
            if (!$fridge) {
                return $this->json([
                    'errors' => "unable to save new floor at this moment."
                ], 400);
            }

            $floor->setName($name);
            $floor->setType($type);
            $floor->setQtyFood(0);
            $floor->setIdFridge($fridge);

            $nbr_floors = $fridge->getNbrFloors();
            $fridge->setNbrFloors($nbr_floors + 1);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fridge);
            $entityManager->flush();
            return $this->json([
                'message' => "floor Created!"
            ], 200);
        }
        catch(\Exception $e)
        {
            return $this->json([
                'errors' => "Unable to save new floor at this time."
            ], 400);
        }

    }

    /**
     * @Route("/{id}", name="floor_edit", methods={"PUT"})
     */
    public function editFloor(Request $request, FloorRepository $floorRepository): Response
    {
        try {
            $id_fridge = $request->attributes->get('id');
            $floor = $floorRepository->findOneById($id_fridge);

            if (!$floor) {
                return $this->json([
                    'errors' => "unable to edit floor at this moment."
                ], 400);
            }

            $data = json_decode($request->getContent(), true);

            $name = $data['name'];
            $type = $data['type'];

            $floor->setName($name);
            $floor->setType($type);

            $this->getDoctrine()->getManager()->flush();

            return $this->json([
                'message' => "Floor Updated!"
            ], 200);

        } catch (\Exception $exception) {
            return $this->json([
                'errors' => "unable to edit floor at this moment."
            ], 400);
        }
    }

    /**
     * @Route("/{id}", name="delete_floor", methods={"DELETE"})
     */
    public function deleteFloor(Request $request, FloorRepository $floorRepository): Response
    {
        try {
            $id_floor = $request->attributes->get('id');
            $floor = $floorRepository->findOneById($id_floor);

            if (!$floor) {
                return $this->json([
                    'errors' => "unable to delete floor at this moment."
                ], 400);
            }

            $fridge = $floor->getIdFridge();
            $nbrFloors = $fridge->getNbrFloors();
            $fridge->setNbrFloors($nbrFloors - 1);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($floor);
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
