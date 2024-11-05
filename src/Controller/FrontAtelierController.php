<?php

namespace App\Controller;

use App\Repository\AtelierRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/atelier', name: 'app_front_atelier')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class FrontAtelierController extends AbstractController
{
    #[Route('', name: '')]
    public function index(AtelierRepository $repository): Response
    {
        if ($this->getUser()->getAbonnement() === null || !in_array($this->getUser()->getAbonnement()->getIdOffre()->getType(), ["Gold"])) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        $list = $repository->findAll();
        return $this->render('front_atelier/index.html.twig', [
            'list' => $list,
        ]);
    }

}
