<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieFormType;
use App\Repository\CategorieRepository;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/back/categorie', name: 'app_back_categories_')]
class BackCategorieController extends AbstractController
{
    public function __construct(private LoggerInterface $logger, private SerializerInterface $serializer)
    {

    }

    //Affichage de tous: + Repository
    #[Route('', name: 'list')]
    public function list(CategorieRepository $repo): Response
    {
        $categories = $repo->findAll();
        return $this->render('back_categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    //Ajout
    #[Route('/ajouter', name: 'add')]
    public function addCategorie(ManagerRegistry $doctrine, Request $request, UploaderService $uploaderService): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieFormType::class, $categorie);
        // $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $posterFile = $form->get('image')->getData();
            if ($posterFile) {
                $posterFileName = $uploaderService->upload($posterFile);
                $categorie->setImage($posterFileName);
            }

            $em = $doctrine->getManager();
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute('app_back_categories_list');
        }
        return $this->render('back_categorie/addCategorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    //Affichage d'1 seul:
    #[Route('/showCategorie/{id}', name: 'showCategorie')]
    public function showCategorie($id, ManagerRegistry $doctrine): Response
    {
        //Trouver le bon Categorie
        $repoC = $doctrine->getRepository(Categorie::class);
        $categorie = $repoC->find($id);


        return $this->render('categorie/showC.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    //Modification:
    #[Route('/edit/{id}', name: 'update')]
    public function update(Request $request, ManagerRegistry $doctrine, Categorie $categorie, UploaderService $uploaderService): Response
    {
        $form = $this->createForm(CategorieFormType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $posterFile = $form->get('image')->getData();
            if ($posterFile) {
                $posterFileName = $uploaderService->upload($posterFile);
                $categorie->setImage($posterFileName);
            }
            $entityManager = $doctrine->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('app_back_categories_list');
        }
        return $this->render('back_categorie/updateCategorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }

//Suppression:
    #[Route('/{id}/delete', name: 'delete')]
    public function deleteCategorie($id, ManagerRegistry $doctrine): Response
    {
        //Trouver le bon Categorie
        $repoC = $doctrine->getRepository(Categorie::class);
        $categorie = $repoC->find($id);
        //Utiliser Manager pour supprimer le categorie trouvé
        $em = $doctrine->getManager();
        $em->remove($categorie);
        $em->flush();

        return $this->redirectToRoute('app_back_categories_list');
    }

    /**
     * @throws ExceptionInterface
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    #[Route('/search', name: 'categoriesSearch')]
    public function searchAction(Request $request, CategorieRepository $categorieRepository): Response
    {
        $searchTerm = $request->query->get('query');

        $categories = $categorieRepository->findByCritere($searchTerm);

        $jsonContent = $this->serializer->serialize($categories, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new Response($jsonContent);
    }

}
