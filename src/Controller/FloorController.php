<?php

namespace App\Controller;

use App\Entity\Floor;
use App\Entity\Fridge;
use App\Form\FloorType;
use App\Repository\FloorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/floor")
 */
class FloorController extends AbstractController
{
    /**
     * @Route("/", name="floor_index", methods={"GET"})
     * @param Fridge $fridge
     * @return Response
     */
    public function index(Request $request): Response
    {
        $id_fridge = $request->query->get('id_fridge');
        $listFloors = $this->getDoctrine()
            ->getRepository(Floor::class)
            ->findFloorsFromFridge($id_fridge);
        return $this->render('floor/index.html.twig', [
            'floors' => $listFloors,
        ]);
    }

    public function generateFloorsFridge (Request $request, Fridge $fridge) : Response
    {
        $floorList = [];
        $entityManager = $this->getDoctrine()->getManager();
        $id_fridge = $request->query->get('id_fridge');

        $listFloors = $this->getDoctrine()
            ->getRepository(Floor::class)
            ->findFloorsFromFridge($id_fridge);
        foreach($listFloors as $floor) {
            array_push($floorList, $floor);
        }

        $nbr_floors = $fridge->getNbrFloors();
        $nbr_floors_real = count($floorList);
        $i = 0;
        if ($nbr_floors_real != $nbr_floors){
            $i = $nbr_floors - $nbr_floors_real;
        }

        while ($i < $nbr_floors) {
            $floor = new Floor();
            $floor->setType('None');
            $floor->setQtyFood(0);
            $floor->setIdFridge($id_fridge);
            $entityManager->persist($floor);
            $entityManager->flush();
        }
        return $this->redirectToRoute('floor_index');
    }

    /**
     * @Route("/new", name="floor_new", methods={"GET","POST"})
     */
    public function newFloor(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $id_fridge = $request->query->get('id_fridge');


    }

    /**
     * @Route("/{id}", name="floor_show", methods={"GET"})
     */
    public function showFloor(Floor $floor): Response
    {
        return $this->render('floor/show.html.twig', [
            'floor' => $floor,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="floor_edit", methods={"GET","POST"})
     */
    public function editFloor(Request $request, Floor $floor): Response
    {
        $form = $this->createForm(FloorType::class, $floor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('floor_index');
        }

        return $this->render('floor/edit.html.twig', [
            'floor' => $floor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("{id_fridge}/{id}", name="floor_delete", methods={"DELETE"})
     */
    public function deleteFloor(Request $request, Floor $floor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$floor->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($floor);
            $entityManager->flush();
        }

        return $this->redirectToRoute('floor_index');
    }
}
