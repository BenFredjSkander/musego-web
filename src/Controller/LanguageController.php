<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LanguageController extends AbstractController
{
    #[Route(path: '/lang/{_locale}', requirements: ['_locale' => 'en|es|de|it|ru'])]
    public function index(Request $request): RedirectResponse
    {
        $locale = $request->getLocale();
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    #[Route('/test')]
    public function test(): Response
    {
        return $this->render('icons-unicons.html.twig');
    }
}
