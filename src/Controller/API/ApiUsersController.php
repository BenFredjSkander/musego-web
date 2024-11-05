<?php

namespace App\Controller\API;

use App\Dto\ApiResponse;
use App\Dto\Response\Transformers\UserDtoTransformer;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/api/users', name: 'api_users_')]
class ApiUsersController extends AbstractController
{
    public function __construct(private UserDtoTransformer $dtoTransformer, private JsonResponseFactory $jsonResponse)
    {
    }

    #[OA\Tag('User')]
    #[Route('/me', name: 'current', methods: 'GET')]
    public function getCurrentUser(UserInterface $user)
    {
        $userDto = $this->dtoTransformer->transformFromObject($user);
        return $this->jsonResponse->create(new ApiResponse("current user", $userDto));

    }

    #[OA\Tag('User')]
    #[Route('/me', name: 'update_current', methods: 'PUT')]
    public function updateUser(Request $request, UserInterface $user, ManagerRegistry $doctrine, UserRepository $userRepository)
    {

        $em = $doctrine->getManager();
        $currentUser = $userRepository->findOneBy(['id' => $user->getId()]);
        $currentUser->setEmail($request->get("email"));
        $currentUser->setPhoneNumber($request->get("phone"));
        $currentUser->setUsername($request->get("username"));

        $em->flush();
        return $this->jsonResponse->create(new ApiResponse("current user updated", null));

    }
}