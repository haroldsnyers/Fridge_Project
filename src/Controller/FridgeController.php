<?php


namespace App\Controller;


use App\Entity\Fridge;
use App\Form\FridgeType;
use App\Repository\FloorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/fridge")
 */
class FridgeController extends AbstractController
{
    /**
     * @Route("/", name="fridgeRepo_show")
     */
    public function index()
    {
        return $this->showFridgeRepo();
    }

    public function getFridge()
    {
        $repository = $this->getDoctrine()->getRepository(Fridge::class);
        $fridge = $repository->findAll();

        return $fridge;
    }

    public function showFridgeRepo()
    {
        $fridgeList = [];
        $listFridge = $this->getFridge();
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

        $form = $this->createForm(FridgeType::class, $fridge);

        $form->handleRequest($request);
        dump("hello");
        if ($form->isSubmitted() && $form->isValid()) {
            dump('submitted');
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $fridge = $form->getData();

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
    }

    /**
     * @Route("/delete/{id}", name="delete_fridge", methods={"DELETE"})
     */
    public function deleteFridge(Request $request, Fridge $fridge) : Response
    {
        if ($this->isCsrfTokenValid('delete'.$fridge->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fridge);
            $entityManager->flush();
        }

        return $this->showFridgeRepo();
    }
}