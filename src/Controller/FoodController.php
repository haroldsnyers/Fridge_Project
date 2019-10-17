<?php

namespace App\Controller;

use App\Entity\Floor;
use App\Entity\Food;
use App\Form\FoodType;
use App\Repository\FoodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("fridge/{fridgeid}/food")
 */
class FoodController extends AbstractController
{
    /**
     * @Route("/floor/{floorId}", name="food_index", methods={"GET"})
     * @param Request $request
     * @param FoodRepository $foodRepository
     * @return Response
     */
    public function index(Request $request, FoodRepository $foodRepository): Response
    {
        $id_floor = $request->attributes->get('floorId');
        $id_fridge = $request->attributes->get('fridgeid');

        return $this->render('food/index.html.twig', [
            'foods' => $foodRepository->findByIdFloor($id_floor),
            'idFridge' => $id_fridge
        ]);
    }

    /**
     * @Route("/new", name="food_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $id_fridge = $request->attributes->get('fridgeid');

        $listFloors = $this->getDoctrine()
            ->getRepository(Floor::class)
            ->findFloorsFromFridge($id_fridge);

        $food = new Food();
        $form = $this->createForm(FoodType::class, $food, ['floors' => $listFloors]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($food);
            $entityManager->flush();

            return $this->redirectToRoute('floor_index', ['fridgeid' => $id_fridge]);
        }

        return $this->render('food/new.html.twig', [
            'fridgeid' => $id_fridge,
            'food' => $food,
            'foodForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="food_show", methods={"GET"})
     */
    public function show(Food $food): Response
    {
        return $this->render('food/show.html.twig', [
            'food' => $food,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="food_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Food $food): Response
    {
        $form = $this->createForm(FoodType::class, $food);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('food_index', [
                'fridgeid' => $food->getIdFloor()->getIdFridge()->getId(),
                'floorId' => $food->getIdFloor()->getId()
            ]);
        }

        return $this->render('food/edit.html.twig', [
            'food' => $food,
            'foodForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="food_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Food $food): Response
    {
        if ($this->isCsrfTokenValid('delete'.$food->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($food);
            $entityManager->flush();
        }

        return $this->redirectToRoute('food_index', [
            'fridgeid' => $food->getIdFloor()->getIdFridge()->getId(),
            'floorId' => $food->getIdFloor()->getId()
        ]);
    }
}
