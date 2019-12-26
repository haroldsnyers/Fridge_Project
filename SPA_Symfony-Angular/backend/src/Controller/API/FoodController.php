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
class FoodController extends AbstractController
{
    /**
     * @Route("/", name="food_index", methods={"GET"})
     * @param Request $request
     * @param FoodRepository $foodRepository
     * @return Response
     */
    public function index(Request $request, FoodRepository $foodRepository, FloorRepository $floorRepository): Response
    {
        $id_floor = $request->query->get('idFloor');
        try {
            $floor = $floorRepository->findOneById($id_floor);
            if (!$floor) {
                return $this->json([
                    'errors' => "unable to fetch foods at this moment."
                ], 400);
            }
            $listFood = $foodRepository->findByIdFloor($id_floor);

            $defaultContext = [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context){
                    return $object->getId();
                },
                ObjectNormalizer::CIRCULAR_REFERENCE_LIMIT =>0,
                AbstractNormalizer::IGNORED_ATTRIBUTES =>['fridge', 'user', 'floor', 'idFridge', "qtyFood"],
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
                'errors' => "unable to fetch foods at this moment."
            ], 400);
        }
    }

    /**
     * @Route("/", name="food_new", methods={"POST"})
     */
    public function newFood(Request $request, FloorRepository $floorRepository): Response
    {
        try {
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

            if (!$floor) {
                return $this->json([
                    'errors' => "unable to save new food at this moment."
                ], 400);
            }

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
     * @Route("/{id}", name="food_edit", methods={"PUT"})
     */
    public function edit(Request $request, FoodRepository $foodRepository): Response
    {
        try {
            $id_food = $request->attributes->get('id');
            $food = $foodRepository->findOneById($id_food);

            if (!$food) {
                return $this->json([
                    'errors' => "unable to edit food at this moment."
                ], 400);
            }

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

            $this->getDoctrine()->getManager()->flush();

            return $this->json([
                'message' => "Food Updated!"
            ]);

        } catch (\Exception $exception) {
            return $this->json([
                'errors' => "unable to edit food at this moment."
            ], 400);
        }
    }

    /**
     * @Route("/{id}", name="food_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FoodRepository $foodRepository, FloorRepository $floorRepository): Response
    {
        try {
            $id_food = $request->attributes->get('id');
            $food = $foodRepository->findOneById($id_food);

            if (!$food) {
                return $this->json([
                    'errors' => "unable to delete food at this moment."
                ], 400);
            }

            $floor = $floorRepository->findOneById($food->getIdFloor());

            $nbr_floors = $floor->getQtyFood();
            $floor->setQtyFood($nbr_floors - 1);

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
