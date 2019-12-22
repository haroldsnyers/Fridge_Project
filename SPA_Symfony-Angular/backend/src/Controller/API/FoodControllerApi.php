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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
        $id_floor = $request->query->get('idFloor');
        try {
            $listFood = $foodRepository->findByIdFloor($id_floor);

            $defaultContext = [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context){
                    return $object->getId();
                },
                ObjectNormalizer::CIRCULAR_REFERENCE_LIMIT =>0,
                AbstractNormalizer::IGNORED_ATTRIBUTES =>['fridge', 'user', 'floor', 'idFridge'],
                ObjectNormalizer::ENABLE_MAX_DEPTH => true,
            ];

            $encoders = array( new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $jsonContent = $serializer->serialize($listFood,'json', $defaultContext);
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
    public function newFood(Request $request, FloorRepository $floorRepository): Response
    {
        $food = new Food();

        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $type = $data['type'];
        $expirationDate = $data['expiration_date'];
        $quantity = $data['quantity'];
        $dateOfPurchase = $data['date_of_purchase'];
        $imageFood = $data['image_food_path'];
        $unitQuantity = $data['unit_qty'];
        $id_floor = $data['id_floor'];

        $floor = $floorRepository->findOneById($id_floor);

        $food->setName($name);
        $food->setType($type);
        $food->setExpirationDate($expirationDate);
        $food->setQuantity($quantity);
        $food->setDateOfPurchase($dateOfPurchase);
        $food->setImageFoodPath($imageFood);
        $food->setUnitQty($unitQuantity);
        $food->setIdFloor($floor);


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
     * @Route("/{id}", name="food_edit", methods={"PUT"})
     */
    public function edit(Request $request, FoodRepository $foodRepository): Response
    {
        $id_food = $request->attributes->get('id');
        $food = $foodRepository->findOneById($id_food);

        $data = json_decode($request->getContent(), true);

        $name = $data['name'];
        $type = $data['type'];
        $expirationDate = $data['expiration_date'];
        $quantity = $data['quantity'];
        $dateOfPurchase = $data['date_of_purchase'];
        $imageFoodPath = $data['image_food_path'];
        $unitQty = $data['unit_qty'];

        $food->setName($name);
        $food->setType($type);
        $food->setExpirationDate($expirationDate);
        $food->setQuantity($quantity);
        $food->setDateOfPurchase($dateOfPurchase);
        $food->setImageFoodPath($imageFoodPath);
        $food->setUnitQty($unitQty);

        try {
            $this->getDoctrine()->getManager()->flush();

            return $this->json([
                'message' => "Food Updated!"
            ]);

        } catch (\Exception $exception) {
            return $this->json([
                'errors' => $exception
            ], 400);
        }
    }

    /**
     * @Route("/{id}", name="food_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FoodRepository $foodRepository, FloorRepository $floorRepository): Response
    {
        $id_food = $request->attributes->get('id');
        $food = $foodRepository->findOneById($id_food);

        $floor = $floorRepository->findOneById($food->getIdFloor());

        $nbr_floors = $floor->getQtyFood();
        $floor->setQtyFood($nbr_floors - 1);

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($food);
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
