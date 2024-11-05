<?php

namespace App\Controller\API;

use App\Dto\ApiResponse;
use App\Dto\Response\Transformers\CategorieDtoTransformer;
use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/categories', name: 'api_')]
class ApiCategoriesController extends AbstractController
{
    public function __construct(
        private readonly JsonResponseFactory     $factory,
        private readonly CategorieDtoTransformer $categorieDtoTransformer
    )
    {
    }

    #[OA\Tag('Categorie')]
    #[Route('/list', name: 'app_api_categories', methods: ['GET'])]
    public function index(CategorieRepository $repo): JsonResponse
    {
        $categories = $repo->findAll();
        $data = $this->categorieDtoTransformer->transformFromObjects($categories);


        return $this->factory->create(new ApiResponse('Categories Successfully Retrieved', $data));
    }

    //Affichage d'1 seul:
    #[OA\Tag('Categorie')]
    #[Route('/showCategorie/{id}', name: 'showCategorie', methods: ['GET'])]
    public function showCategorie($id, ManagerRegistry $doctrine): Response
    {
        //Trouver la bonne catégorie
        $repoC = $doctrine->getRepository(Categorie::class);
        $categorie = $repoC->find($id);

        // Vérifier si la catégorie existe
        if (!$categorie) {
            throw $this->createNotFoundException('Cette catégorie n\'existe pas.');
        }

        $categorieDto = $this->categorieDtoTransformer->transformFromObjects($categorie);
        // Créer la réponse API
        $apiResponse = new ApiResponse('Catégorie récupérée avec succès', $categorieDto);
        $response = $this->factory->create($apiResponse, 200);

        return $response;
    }

}