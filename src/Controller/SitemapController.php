<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'app_sitemap')]
    public function index(): Response
    {
        $urls = [];
        $urls[] = [
            'loc' => $this->generateUrl('app_front_home', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'changefreq' => 'hourly',
            'priority' => '1.00',
        ];
        $urls[] = [
            'loc' => $this->generateUrl('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'changefreq' => 'hourly',
            'priority' => '1.00',
        ];
        $urls[] = [
            'loc' => $this->generateUrl('app_register', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'changefreq' => 'hourly',
            'priority' => '1.00',
        ];
        $urls[] = [
            'loc' => $this->generateUrl('app_front_offre_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'changefreq' => 'hourly',
            'priority' => '1.00',
        ];
        $response = new Response(
            $this->renderView('sitemap/index.html.twig', ['urls' => $urls]),
        );
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }
}
