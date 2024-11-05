<?php

namespace App\Controller;

use App\Form\ProfileUpdateType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile', name: 'app_front_profile_')]
class FrontProfileController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine)
    {
    }

    #[Route(name: 'index')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('front_profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit', name: 'edit')]
    public function edit(Request $request): Response
    {
        $form = $this->createForm(ProfileUpdateType::class, $this->getUser());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->getManager()->flush();
            return $this->redirectToRoute('app_front_profile_index');
        }
        return $this->renderForm("front_profile/edit.html.twig",
            ['form' => $form]);

    }
}
