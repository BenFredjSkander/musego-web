<?php

namespace App\Controller\API;

use App\Dto\ApiResponse;
use App\Dto\Response\Transformers\OffreDtoTransformer;
use App\Repository\OffreRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api/offres', name: 'api_')]
class ApiOffreController extends AbstractController
{
    public function __construct(private OffreDtoTransformer $offreDtoTransformer, private JsonResponseFactory $jsonResponseFactory)
    {
    }

    #[OA\Tag('Offre')]
    #[Route('/list', name: 'offres', methods: 'GET')]
    public function getOffres(OffreRepository $repository): Response
    {
        $offres = $repository->findAll();
        $dto = $this->offreDtoTransformer->transformFromObjects($offres);

        $response = new ApiResponse("offres list", $dto);
        return $this->jsonResponseFactory->create($response);
    }

    #[OA\Tag('Offre')]
    #[Route('/get', name: 'offre', methods: 'GET')]
    public function getOffre(OffreRepository $repository, Request $request): Response
    {

        $idoffre = $request->query->get('idoffre');
        $offre = $repository->find($idoffre);

        if ($offre) {
            $dto = $this->offreDtoTransformer->transformFromObject($offre);
        } else {
            $dto = null;
        }

        $response = new ApiResponse("offre ", $dto);
        return $this->jsonResponseFactory->create($response);
    }

}