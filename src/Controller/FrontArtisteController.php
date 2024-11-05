<?php

namespace App\Controller;

use App\Entity\Artiste;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class FrontArtisteController extends AbstractController
{

    #[Route('/artiste/{id}', name: 'app_front_artiste_showArtiste')]
    public function showArtiste($id, ManagerRegistry $doctrine): Response
    {
        if ($this->getUser()->getAbonnement() === null) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        //Trouver le bon Artiste
        $repoC = $doctrine->getRepository(Artiste::class);
        $artiste = $repoC->find($id);


        return $this->render('front_artiste/index.html.twig', [
            'artiste' => $artiste,
        ]);
    }
}
