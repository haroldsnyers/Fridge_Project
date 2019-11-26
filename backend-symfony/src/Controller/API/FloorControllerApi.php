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
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/floors")
 */
class FloorControllerApi extends AbstractController
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
            $this->generateFloorsFridge($id_fridge, $fridgeRepository);
            $listFloors = $this->getFloorsFridge($id_fridge);

            $encoders = array( new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $jsonContent = $serializer->serialize($listFloors,'json', [
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

    //public function updateNbrFood()

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
    }

    /**
     * @Route("/", name="floor_new", methods={"POST"})
     * @param Request $request
     */
    public function addFloor (Request $request, FridgeRepository $fridgeRepository)
    {
        $floor = new Floor();

        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $type = $data['type'];
        $id_fridge = $data['id_fridge'];

        $fridge = $fridgeRepository->findOneById($id_fridge);

        $floor->setName($name);
        $floor->setType($type);
        $floor->setQtyFood(0);
        $floor->setIdFridge($fridge);

        $nbr_floors = $fridge->getNbrFloors();
        $fridge->setNbrFloors($nbr_floors + 1);

        try
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fridge);
            $entityManager->flush();
            return $this->json([
                'message' => "floor Created!"
            ]);
        }
        catch(\Exception $e)
        {
            return $this->json([
                'errors' => "Unable to save new floor at this time."
            ], 400);
        }

    }

    public function changeNbrFloor ($id_fridge, $change)
    {
        $fridge = $this->getDoctrine()
            ->getRepository(Fridge::class)
            ->findOneById($id_fridge);

        $nbr_floors = $fridge->getNbrFloors();

        $entityManager = $this->getDoctrine()->getManager();
        $fridge->setNbrFloors($nbr_floors + $change);
        $entityManager->persist($fridge);
        $entityManager->flush();
    }

    /**
     * @Route("/{id}", name="floor_edit", methods={"PUT"})
     */
    public function editFloor(Request $request, FloorRepository $floorRepository, Floor $floor): Response
    {
        $id_fridge = $request->attributes->get('id');
        $floor = $floorRepository->findOneById($id_fridge);

        $data = json_decode($request->getContent(), true);

        $name = $data['name'];
        $type = $data['type'];

        $floor->setName($name);
        $floor->setType($type);

        try {
            $this->getDoctrine()->getManager()->flush();

            return $this->json([
                'message' => "Floor Updated!"
            ]);

        } catch (\Exception $exception) {
            return $this->json([
                'errors' => $exception
            ], 400);
        }
    }

    /**
     * @Route("/{id}", name="delete_floor", methods={"DELETE"})
     */
    public function deleteFloor(Request $request, FloorRepository $floorRepository): Response
    {
        $id_floor = $request->attributes->get('id');
        $floor = $floorRepository->findOneById($id_floor);

        $fridge = $floor->getIdFridge();
        $nbrFloors = $fridge->getNbrFloors();
        $fridge->setNbrFloors($nbrFloors - 1);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($floor);
        $entityManager->flush();

        try {
            $this->getDoctrine()->getManager()->flush();

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
