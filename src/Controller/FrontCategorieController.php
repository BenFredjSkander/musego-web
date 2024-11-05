<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories', name: 'app_front_categorie_')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class FrontCategorieController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(CategorieRepository $repo): Response
    {
        if ($this->getUser()->getAbonnement() === null) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        $categories = $repo->findAll();
        return $this->render('front_categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    //Affichage d'1 seul:
    #[Route('/{id}', name: 'showCategorie')]
    public function showCategorie($id, ManagerRegistry $doctrine): Response
    {
        if ($this->getUser()->getAbonnement() === null) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        //Trouver le bon Categorie
        $repoC = $doctrine->getRepository(Categorie::class);
        $categorie = $repoC->find($id);


        return $this->render('front_oeuvre/index.html.twig', [
            'categorie' => $categorie,
        ]);
    }
}
