<?php

namespace App\Controller\API;

use App\Entity\Floor;
use App\Entity\Food;
use App\Form\FoodType;
use App\Repository\FloorRepository;
use App\Repository\FoodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("api/food")
 */
class FoodControllerApi extends AbstractController
{
    /**
     * @Route("/", name="food_index", methods={"GET"})
     * @param Request $request
     * @param FoodRepository $foodRepository
     * @return Response
     */
    public function index(Request $request, FoodRepository $foodRepository): Response
    {
        $id_floor = $request->query->get('floorId');

        try {
            $listFood = $foodRepository->findByIdFloor($id_floor);
            $encoders = array( new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $jsonContent = $serializer->serialize($listFood,'json', [
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
     * @Route("/", name="food_new", methods={"POST"})
     */
    public function new(Request $request, FloorRepository $floorRepository): Response
    {
        $food = new Food();

        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $type = $data['type'];
        $expirationDate = $data['type'];
        $quantity = $data['type'];
        $dateOfPurchase = $data['type'];
        $imageFood = $data['type'];
        $unitQuantity = $data['type'];
        $id_floor = $data['id_fridge'];

        $floor = $floorRepository->findOneById($id_floor);

        $food->setName($name);
        $food->setType($type);
        $food->setExpirationDate($expirationDate);
        $food->setQuantity($quantity);
        $food->setDateOfPurchase($dateOfPurchase);
        $food->setImageFoodPath($imageFood);
        $food->setUnitQty($unitQuantity);
        $food->setIdFloor($id_floor);


        $nbr_floors = $floor->getQtyFood();
        $floor->setQtyFood($nbr_floors + 1);

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($food);
            $entityManager->flush();
            return $this->json([
                'message' => "food Created!"
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'errors' => "Unable to save new food at this time."
            ], 400);
        }
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
