<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\Offre;
use App\Repository\AbonnementRepository;
use App\Repository\OffreRepository;
use App\Service\EmailService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/offres', name: 'app_front_offre_')]
class FrontOffreController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function index(OffreRepository $repository): Response
    {

        $list = $repository->findAll();

        $nbpromo = $repository->findPromoCount();

        $nbnopromo = $repository->findNoPromoCount();
        return $this->render('front_offre/index.html.twig', [
            'offres' => $list,'offrespromo' => $nbpromo,'offresnopromo' => $nbnopromo
        ]);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    #[Route('/detail/{id}', name: 'show')]
    public function show($id, ManagerRegistry $doctrine,AbonnementRepository $repository): Response
    {
        $repo = $doctrine->getRepository(Offre::class);
        $offre = $repo->find($id);

        $count=$repository->existUserandOffre($this->getUser(),$offre->getId());
        if($count!=0) {
            $listusoff = $repository->findByUserandOffre($this->getUser(), $offre->getId());

            return $this->render('front_offre/payment.html.twig', [
                'showoffre' => $offre, 'issubbed' => $listusoff
            ]);
        }
        return $this->render('front_offre/payment.html.twig', [
            'showoffre' => $offre, 'issubbed' => null
        ]);
    }

}
