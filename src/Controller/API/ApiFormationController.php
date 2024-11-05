<?php

namespace App\Controller\API;

use App\Dto\ApiResponse;
use App\Dto\Response\Transformers\FormationDtoTransformer;
use App\Repository\FormationRepository;
use App\Service\EmailService;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/api/formation', name: 'api_')]
class ApiFormationController extends AbstractController
{
    public function __construct(
        private FormationDtoTransformer $formationDtoTransformer,
        private JsonResponseFactory     $jsonResponseFactory
    )
    {
    }

    #[OA\Tag('Formation')]
    #[Route('/', name: 'formations', methods: 'GET')]
    public function getAllFormations(FormationRepository $repository): Response
    {
        $formations = $repository->findAll();
        $dto = $this->formationDtoTransformer->transformFromObjects($formations);

        $response = new ApiResponse("formations list", $dto);
        return $this->jsonResponseFactory->create($response);
    }

    #[OA\Tag('Formation')]
    #[Route('/{id}', name: 'detail Formation', methods: 'GET')]
    public function getFormation(
        FormationRepository     $formationRepository,
        int                     $id,
        FormationDtoTransformer $formationDtoTransformer
    ): Response
    {

        $formation = $formationRepository->find($id);
        $formationDto = $formationDtoTransformer->transformFromObject($formation);
        $response = new ApiResponse("details d'une formation", $formationDto);
        return $this->jsonResponseFactory->create($response);
    }

    #[OA\Tag('Formation')]
    #[Route('/atelier/{id}', name: '_byAtelier', methods: 'GET')]
    public function showFormationByAtelier(
        FormationRepository $repository,
                            $id
    ): Response
    {
        $list = $repository->FormationByAtelier($id);
        $dto = $this->formationDtoTransformer->transformFromObjects($list);
        $response = new ApiResponse("Participation à une Formation Confirmée", $dto);
        return $this->jsonResponseFactory->create($response);
    }

    #[OA\Tag('Formation')]
    #[Route('/participer/{id}', name: '_participer', methods: 'POST')]
    public function Participer(
        $id,
        FormationRepository $repository,
        ManagerRegistry $doctrine,
        EmailService $emailService,
        UserInterface $user
    ): Response
    {
        $formation = $repository->find($id);
        $dto = null;
        if ($formation->conatinsIdUser($user)) {
            $message = 'Vous avez annulé votre participation à cette formation !';
            $formation->removeIdUser($user);


        } else {
            $formation->addIdUser($user);
            $message = 'Inscription Confirmée à la Formation' . $formation->getNom();
            $emailService->sendTemplatedEmail(
                $user->getEmail(),
                [
                    'FORMATION_NAME' => $formation->getNom(),
                    'FORMATION_BEGIN_DATE' => $formation->getDateDebut()->format('Y-m-d'),
                    'FORMATION_END_DATE' => $formation->getDateFin()->format('Y-m-d')
                ],
                3
            );
            $dto = $this->formationDtoTransformer->transformFromObject($formation);


        }
        $em = $doctrine->getManager();
        $em->persist($formation);
        $em->flush();
        $response = new ApiResponse($message, $dto);
        return $this->jsonResponseFactory->create($response);
    }

    #[OA\Tag('Formation')]
    #[Route('/isparticipated/{id}', name: '_participated', methods: 'GET')]
    public function isParticipated($id, FormationRepository $repository, UserInterface $user): Response
    {
        $formation = $repository->find($id);
        $dto = null;
        if ($formation->conatinsIdUser($user)) {
            $message = 'User déjà inscrit à cette formation !';

        } else {
            $message = 'User n\'est pas inscrit à la formation ' . $formation->getNom();

        }
        $response = new ApiResponse($message, $dto);
        return $this->jsonResponseFactory->create($response);
    }
}