<?php

namespace App\Controller;

use App\Entity\Oeuvre;
use App\Form\OeuvreFormType;
use App\Repository\OeuvreRepository;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/back/oeuvre', name: 'app_back_oeuvres_')]
class BackOeuvreController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer)
    {

    }

    #[Route('', name: 'list')]
    public function list(OeuvreRepository $repo): Response
    {
        $oeuvres = $repo->findAll();
        return $this->render('back_oeuvre/index.html.twig', [
            'oeuvres' => $oeuvres,
        ]);
    }

    //Ajout
    #[Route('/ajouter', name: 'add')]
    public function addOeuvre(ManagerRegistry $doctrine, Request $request, UploaderService $uploaderService): Response
    {
        $oeuvre = new Oeuvre();
        $form = $this->createForm(OeuvreFormType::class, $oeuvre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $posterFile = $form->get('image')->getData();
            if ($posterFile) {
                $posterFileName = $uploaderService->upload($posterFile);
                $oeuvre->setImage($posterFileName);
            }
            $em = $doctrine->getManager();
            $em->persist($oeuvre);
            $em->flush();


            $this->addFlash('success', 'Oeuvre ajoutée avec succès !');

            return $this->redirectToRoute('app_back_oeuvres_list');
            //najouti fentre messageeeeeeeeeeeeeeeeeee
        }
        return $this->render('back_oeuvre/addOeuvre.html.twig', [
            'form' => $form->createView(),
        ]);

    }


    //Modification:
    #[Route('/{id}/edit', name: 'update')]
    public function updateOeuvre(Request $request, ManagerRegistry $doctrine, Oeuvre $oeuvre, UploaderService $uploaderService)
    {
        $form = $this->createForm(OeuvreFormType::class, $oeuvre);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $posterFile = $form->get('image')->getData();
            if ($posterFile) {
                $posterFileName = $uploaderService->upload($posterFile);
                $oeuvre->setImage($posterFileName);
            }
            $entityManager = $doctrine->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('app_back_oeuvres_list');
        }
        return $this->render('back_oeuvre/updateOeuvre.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    //Suppression:
    #[Route('/{id}/delete', name: 'delete')]
    public function deleteOeuvre($id, ManagerRegistry $doctrine): Response
    {
        //Trouver le bon Oeuvre
        $repoC = $doctrine->getRepository(Oeuvre::class);
        $oeuvre = $repoC->find($id);
        //Utiliser Manager pour supprimer le oeuvre trouvé
        $em = $doctrine->getManager();
        $em->remove($oeuvre);
        $em->flush();

        return $this->redirectToRoute('app_back_oeuvres_list');
    }


    /**
     * @throws ExceptionInterface
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    #[Route('/search', name: 'oeuvresSearch')]
    public function searchAction(Request $request, OeuvreRepository $oeuvreRepository): Response
    {
        $searchTerm = $request->query->get('query');

        $oeuvres = [];

        if ($searchTerm !== null && $searchTerm !== '') {
            $oeuvres = $oeuvreRepository->findByCritere($searchTerm);
        }
        $jsonContent = $this->serializer->serialize($oeuvres, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new Response($jsonContent);
    }


    #[Route('/triOeuvres', name: 'triOeuvres')]
    public function triOeuvres(Request $request): Response
    {
        $colonne = $request->query->get('colonne', 'titre');
        $sens = $request->query->get('sens', 'ASC');

        $oeuvres = $this->getDoctrine()->getRepository(Oeuvre::class)->trierParColonne($colonne, $sens);

        // Redirige vers la nouvelle page triée
        $routeName = $this->generateUrl('/back/oeuvre/triOeuvresTitre', ['colonne' => $colonne, 'sens' => $sens]);
        return $this->redirect($routeName);
    }

    #[Route('/triOeuvresTitre', name: 'triOeuvresT')]
    public function triOeuvresTitre(Request $request): Response
    {
        $colonne = $request->query->get('colonne', 'titre');
        $sens = $request->query->get('sens', 'ASC');

        $oeuvres = $this->getDoctrine()->getRepository(Oeuvre::class)->trierParColonne($colonne, $sens);

        return $this->render('back_oeuvre/triO.html.twig', [
            'oeuvres' => $oeuvres
        ]);
    }

}
