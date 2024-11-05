<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackDashboardController extends AbstractController
{
    #[Route('/back/dashboard', name: 'app_back_dashboard')]
    public function index(): Response
    {
        return $this->render('back_dashboard/index.html.twig', [
            'controller_name' => 'BackDashboardController',
        ]);
    }
}
