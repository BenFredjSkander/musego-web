<?php

namespace App\Controller;

use App\Entity\Sponsor;
use App\Form\SponsorType;
use App\Repository\SponsorRepository;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/back/sponsors', name: 'app_back_')]
class BackSponsorController extends AbstractController
{


    #[Route(name: 'sponsors')]
    public function afficherSponsors(SponsorRepository $sponsorRepository,): Response
    {
        $result = $sponsorRepository->findAll();

        return $this->render('back_sponsor/liste_sponsors_back.html.twig', [
            'sponsors' => $result,

        ]);


    }


    #[Route('/add', name: 'sponsor_add')]
    public function ajouterSponsor(ManagerRegistry $doctrine, Request $request, UploaderService $fileUploader): Response
    {

        $sponsor = new Sponsor();
        $form = $this->createForm(SponsorType::class, $sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $sponsor->setPhoto($brochureFileName);
            }


            $em = $doctrine->getManager();
            $em->persist($sponsor);
            $em->flush();

            return $this->redirectToRoute('app_back_sponsors');
        }

        return $this->render('back_Sponsor/ajouter_sponsor_back.html.twig', [
            'sponsor' => $sponsor,
            'form' => $form->createView(),
        ]);

    }


    #[Route('/remove/{id}', name: 'sponsor_remove')]
    public function Supprimersponsor(ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $sponsor = $em->getRepository(Sponsor::class)->find($id);

        if (!$sponsor) {
            throw $this->createNotFoundException('Le sponsor avec l\'id ' . $id . ' n\'existe pas.');
        }

        $em->remove($sponsor);
        $em->flush();

        return $this->redirectToRoute('app_back_sponsors');
    }


    #[Route('/update/{id}', name: 'sponsor_update')]
    public function modifiersponsor(Request $request, ManagerRegistry $doctrine, Sponsor $sponsor, UploaderService $fileUploader): Response
    {

        $form = $this->createForm(SponsorType::class, $sponsor);

        $em = $doctrine->getManager();


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $sponsor->setPhoto($brochureFileName);
            }
            $entityManager = $doctrine->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('app_back_sponsors');
        }
        return $this->render('back_sponsor/modifier_sponsor_back.html.twig', [
            'form' => $form->createView(),
        ]);

    }


}
