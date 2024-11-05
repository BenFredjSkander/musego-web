<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontAboutController extends AbstractController
{
    #[Route('/apropos', name: 'app_front_about')]
    public function index(): Response
    {
        return $this->render('front_about/index.html.twig', [
            'controller_name' => 'FrontAboutController',
        ]);
    }
}
