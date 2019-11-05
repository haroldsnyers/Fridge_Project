<?php

namespace App\Controller\API;

use App\Entity\Floor;
use App\Entity\Food;
use App\Form\FoodType;
use App\Repository\FoodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @Route("api/fridge/{fridgeid}/food")
 */
class FoodControllerApi extends AbstractController
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
        $user = $this->getUser()->getUsername();
        $fridgeUser = $foodRepository->findByIdFloor($id_floor)[0]->getIdFloor()->getIdFridge()->getUser()->getUsername();

        if ($user == $fridgeUser) {
            $id_fridge = $request->attributes->get('fridgeid');

            return $this->render('food/index.html.twig', [
                'foods' => $foodRepository->findByIdFloor($id_floor),
                'idFridge' => $id_fridge
            ]);

        } else {
            return $this->render('home/homepage.html.twig');
        }
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
            /** @var UploadedFile $ImageFile */
            $noImage = 'images/Food.jpg';
            $this->uploadImage($form, $food, $noImage);

            // ... persist the $product variable or any other work
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

    public function uploadImage(FormInterface $form, Food $food, $noImageUpload)
    {
        $ImageFile = $form->get('imageFood')->getData();

        // this condition is needed because the 'image' field is not required
        // so the PDF file must be processed only when a file is uploaded
        if ($ImageFile) {
            $originalFilename = pathinfo($ImageFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $ImageFile->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $ImageFile->move(
                    'images/fridge',
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            $filePath = 'images/fridge' . '/' . $newFilename;
            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $food->setImageFoodPath($filePath);
        } else {
            $food->setImageFoodPath($noImageUpload);
        }
    }

    /**
     * @Route("/{id}/edit", name="food_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Food $food): Response
    {
        $user = $this->getUser()->getUsername();
        $fridgeUser = $food->getIdFloor()->getIdFridge()->getUser()->getUsername();

        if ($user == $fridgeUser) {
            $form = $this->createForm(FoodType::class, $food);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $noImage = $food->getImageFoodPath();
                $this->uploadImage($form, $food, $noImage);

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

        } else {
            return $this->render('home/homepage.html.twig');
        }
    }

    /**
     * @Route("/{id}/delete", name="food_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Food $food): Response
    {
        $user = $this->getUser()->getUsername();
        $fridgeUser = $food->getIdFloor()->getIdFridge()->getUser()->getUsername();

        if ($user == $fridgeUser) {
            if ($this->isCsrfTokenValid('delete'.$food->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();

                // deleting file after deleting food
                $filename = $food->getImageFoodPath();
                $filesystem = new Filesystem();
                $filesystem->remove($filename);

                $entityManager->remove($food);
                $entityManager->flush();
            }

            return $this->redirectToRoute('food_index', [
                'fridgeid' => $food->getIdFloor()->getIdFridge()->getId(),
                'floorId' => $food->getIdFloor()->getId()
            ]);

        } else {
            return $this->render('home/homepage.html.twig');
        }
    }
}
