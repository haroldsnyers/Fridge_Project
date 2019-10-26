<?php

namespace App\Controller\API;

use App\Entity\Floor;
use App\Entity\Fridge;
use App\Form\FloorType;
use App\Repository\FoodRepository;
use App\Repository\FridgeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/fridge/{fridgeid}/floor")
 */
class FloorControllerApi extends AbstractController
{
    /**
     * @Route("/", name="floor_index", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, FoodRepository $foodRepository, FridgeRepository $fridgeRepository): Response
    {
        $id_fridge = $request->attributes->get('fridgeid');

        $user = $this->getUser()->getUsername();
        $fridgeUser = $fridgeRepository->findOneById($id_fridge)->getUser()->getUsername();

        if ($user == $fridgeUser) {
            $this->generateFloorsFridge($request, $id_fridge);
            $listFloors = $this->getFloorsFridge($id_fridge);

            foreach ($listFloors as $floor) {
                $qty_food = count($foodRepository->findByIdFloor($floor));
                $floor->setQtyFood($qty_food);
            }

            return $this->render('floor/index.html.twig', [
                'floors' => $listFloors,
                'fridgeid' => $id_fridge,
            ]);

        } else {
            return $this->render('home/homepage.html.twig');
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

    public function generateFloorsFridge (Request $request, $id_fridge)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $floorList = $this->getFloorsFridge($id_fridge);
        $fridge = $this->getDoctrine()
            ->getRepository(Fridge::class)
            ->findOneById($id_fridge);

        $nbr_floors = $fridge->getNbrFloors();
        $nbr_floors_real = count($floorList);
        $i = 0;
        if ($nbr_floors_real != $nbr_floors){
            $i = $nbr_floors - $nbr_floors_real;
        }

        while ($i != 0) {
            $floor = new Floor();
            $floor->setName('bottom floor');
            $floor->setType('None');
            $floor->setQtyFood(0);
            $floor->setIdFridge($fridge);
            $entityManager->persist($floor);
            $entityManager->flush();
            $i--;
        }
    }

    /**
     * @Route("/new", name="floor_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addFloor (Request $request)
    {
        $id_fridge = $request->attributes->get('fridgeid');
        $this->changeNbrFloor($id_fridge, 1);

        return $this->redirectToRoute('floor_index', [
            'fridgeid' => $id_fridge
        ]);
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
     * @Route("/{id}", name="floor_show", methods={"GET"})
     */
    public function showFloor(Floor $floor): Response
    {
        $user = $this->getUser()->getUsername();
        $fridgeUser = $floor->getIdFridge()->getUser()->getUsername();

        if ($user == $fridgeUser) {
            return $this->render('', [
                'floor' => $floor,
            ]);

        } else {
            return $this->render('home/homepage.html.twig');
        }

    }

    /**
     * @Route("/{id}/edit", name="floor_edit", methods={"GET","POST"})
     */
    public function editFloor(Request $request, Floor $floor): Response
    {
        $user = $this->getUser()->getUsername();
        $fridgeUser = $floor->getIdFridge()->getUser()->getUsername();

        if ($user == $fridgeUser) {
            $form = $this->createForm(FloorType::class, $floor);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                // get id of fridge to generate correct url
                // getIdFridge returns an object fridge
                return $this->redirectToRoute('floor_index', [
                    'fridgeid' => $floor->getIdFridge()->getId()
                ]);
            }

            return $this->render('floor/edit.html.twig', [
                'floor' => $floor,
                'floorForm' => $form->createView(),
            ]);

        } else {
            return $this->render('home/homepage.html.twig');
        }
    }

    /**
     * @Route("/{id}/delete", name="floor_delete", methods={"DELETE"})
     */
    public function deleteFloor(Request $request, Floor $floor): Response
    {
        $user = $this->getUser()->getUsername();
        $fridgeUser = $floor->getIdFridge()->getUser()->getUsername();

        if ($user == $fridgeUser) {
            if ($this->isCsrfTokenValid('delete'.$floor->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($floor);
                $entityManager->flush();
            }

            $id_fridge = $floor->getIdFridge()->getId();
            $this->changeNbrFloor($id_fridge, -1);

            return $this->redirectToRoute('floor_index', [
                'fridgeid' => $floor->getIdFridge()->getId()
            ]);

        } else {
            return $this->render('home/homepage.html.twig');
        }
    }
}
