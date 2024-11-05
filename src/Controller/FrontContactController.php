<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontContactController extends AbstractController
{
    #[Route('/contact', name: 'app_front_contact')]
    public function index(): Response
    {
        return $this->render('front_contact/index.html.twig', [
            'controller_name' => 'FrontContactController',
        ]);
    }
}
