<?php

namespace App\Controller\API;

use App\Dto\ApiResponse;
use App\Dto\Response\Transformers\AtelierDtoTransformer;
use App\Repository\AtelierRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/atelier', name: 'api_')]
class ApiAtelierController extends AbstractController
{
    public function __construct(private AtelierDtoTransformer $atelierDtoTransformer, private JsonResponseFactory $jsonResponseFactory)
    {
    }

    #[OA\Tag('Atelier')]
    #[Route('/', name: 'ateliers', methods: 'GET')]
    public function getAllAteliers(AtelierRepository $repository): Response
    {
        $ateliers = $repository->findAll();
        $dto = $this->atelierDtoTransformer->transformFromObjects($ateliers);

        $response = new ApiResponse("ateliers list", $dto);
        return $this->jsonResponseFactory->create($response);
    }

    #[OA\Tag('Atelier')]
    #[Route('/{id}', name: 'detail Atelier', methods: 'GET')]
    public function getAtelier(AtelierRepository $atelierRepository, $id, AtelierDtoTransformer $atelierDtoTransformer): Response
    {

        $atelier = $atelierRepository->find($id);
        $atelierDto = $atelierDtoTransformer->transformFromObject($atelier);
        $response = new ApiResponse("details d'un Atelier", $atelierDto);
        return $this->jsonResponseFactory->create($response);
    }


}