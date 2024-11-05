<?php

namespace App\Controller\API;

use App\Dto\ApiResponse;
use App\Dto\Response\Transformers\ArtisteDtoTransformer;
use App\Entity\Artiste;
use App\Repository\ArtisteRepository;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/artistes', name: 'api_')]
class ApiArtistesController extends AbstractController
{
    public function __construct(private readonly JsonResponseFactory $factory, private readonly ArtisteDtoTransformer $artisteDtoTransformer)
    {
    }

    /**
     * Artists list
     */
    #[OA\Tag('Artiste')]
    #[Route('/list', name: 'app_api_artistes', methods: ['GET'])]
    public function index(ArtisteRepository $repo): JsonResponse
    {
        $artistes = $repo->findAll();
        $data = $this->artisteDtoTransformer->transformFromObjects($artistes);


        return $this->factory->create(new ApiResponse('artistes Successfully Retrieved', $data));
    }

    /**
     * Artiste details
     */
    #[OA\Tag('Artiste')]
    #[Route('/showArtiste/{id}', name: 'showArtiste', methods: ['GET'])]
    public function showArtiste($id, ManagerRegistry $doctrine): Response
    {
        //Trouver le bon Artiste
        $repoC = $doctrine->getRepository(Artiste::class);
        $artiste = $repoC->find($id);


        $artisteDto = $this->artisteDtoTransformer->transformFromObject($artiste);
        // Créer la réponse API
        $apiResponse = new ApiResponse('Artiste récupéré avec succès', $artisteDto);
        $response = $this->factory->create($apiResponse, 200);

        return $response;
    }

}