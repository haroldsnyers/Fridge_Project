<?php


namespace App\Controller\API;


use App\Entity\Fridge;
use App\Form\FridgeType;
use App\Repository\FloorRepository;
use App\Repository\FridgeRepository;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/fridge")
 */
class FridgeControllerApi extends AbstractController
{
    /**
     * @Route("/", name="fridgeRepo_show")
     */
    public function index()
    {
        return $this->showFridgeRepo();
    }

    public function showFridgeRepo()
    {
        $user = $this->getUser()->getUsername();
        $fridgeList = [];
        $listFridge = $this->getUser()->getListFridges();
        foreach($listFridge as $fridge) {
            array_push($fridgeList, $fridge);
        }
        return $this->render('fridge/showFridgeRep.html.twig', [
            'listFridge' => $fridgeList
        ]);
    }

    /**
     * @Route("/create", name="create_product")
     */
    public function newFridge(Request $request)
    {
        // creates a task object and initializes some data for this example
        $fridge = new Fridge();
        $user = $this->getUser();

        $form = $this->createForm(FridgeType::class, $fridge);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump('submitted');
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $fridge = $form->getData();
            $fridge->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($fridge);
            $entityManager->flush();

            return $this->redirectToRoute('fridgeRepo_show');
        }

        return $this->render('fridge/CreateFridge.html.twig', [
            'fridgeForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="fridge_edit", methods={"GET","POST"})
     */
    public function editFridge(Request $request, Fridge $fridge): Response
    {
        $user = $this->getUser()->getUsername();
        $fridgeUser = $fridge->getUser()->getUsername();

        if ($user == $fridgeUser) {
            $form = $this->createForm(FridgeType::class, $fridge);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('fridgeRepo_show');
            }

            return $this->render('fridge/EditFridge.html.twig', [
                'fridge' => $fridge,
                'fridgeForm' => $form->createView(),
            ]);

        } else {
            return $this->render('home/homepage.html.twig');
        }
    }

    /**
     * @Route("/delete/{id}", name="delete_fridge", methods={"DELETE"})
     */
    public function deleteFridge(Request $request, Fridge $fridge) : Response
    {
        $user = $this->getUser()->getUsername();
        $fridgeUser = $fridge->getUser()->getUsername();

        if ($user == $fridgeUser) {
            if ($this->isCsrfTokenValid('delete'.$fridge->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($fridge);
                $entityManager->flush();
            }

            return $this->showFridgeRepo();
        } else {
            return $this->render('home/homepage.html.twig');
        }
    }
}