<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Repository\FormationRepository;
use App\Service\EmailService;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/formation', name: 'app_front_formation')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class FrontFormationController extends AbstractController
{
    #[Route('/atelier/{id}', name: '_byAtelier')]
    public function showFormationByAtelier(FormationRepository $repository, $id, Atelier $atelier): Response
    {
        if ($this->getUser()->getAbonnement() === null || !in_array($this->getUser()->getAbonnement()->getIdOffre()->getType(), ["Gold"])) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        $list = $repository->FormationByAtelier($id);
        return $this->render('front_atelier/formation.html.twig', [
            'list' => $list, 'id' => $id, 'atelier' => $atelier->getNom()
        ]);
    }

    #[Route('/detail/{id}', name: '_showDetail')]
    public function show($id, FormationRepository $repository): Response
    {
        if ($this->getUser()->getAbonnement() === null || !in_array($this->getUser()->getAbonnement()->getIdOffre()->getType(), ["Gold"])) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        $formation = $repository->find($id);


        return $this->render('front_atelier/detail.html.twig', [
            'formation' => $formation, 'id' => $id,
        ]);
    }

    #[Route('/participer/{id}', name: '_participer')]
    public function Participer($id, FormationRepository $repository, ManagerRegistry $doctrine, EmailService $emailService): Response
    {
        if ($this->getUser()->getAbonnement() === null || !in_array($this->getUser()->getAbonnement()->getIdOffre()->getType(), ["Gold"])) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        $user = $this->getUser();
        $formation = $repository->find($id);
        $formation->addIdUser($user);
        // dd($user);
        // dd($formation->getNom());
        // var_dump($formation);
        $emailService->sendTemplatedEmail(
            $user->getEmail(),
            [
                'FORMATION_NAME' => $formation->getNom(),
                'FORMATION_BEGIN_DATE' => $formation->getDateDebut()->format('Y-m-d'),
                'FORMATION_END_DATE' => $formation->getDateFin()->format('Y-m-d')
            ],
            3);
//        $emailService->sendTextEmail($user->getEmail(), 'Votre participation à la formation ' . $formation->getNom() . ' qui aura lieu le ' . $formation->getDateDebut()->format('Y-m-d') . ' jusqu\'au ' . $formation->getDateFin()->format('Y-m-d') . ' a été retenue');

        $em = $doctrine->getManager();
        $em->persist($formation);
        $em->flush();

        return new Response(true);
    }

}
