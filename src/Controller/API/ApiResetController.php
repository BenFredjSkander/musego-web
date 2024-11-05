<?php

namespace App\Controller\API;

use App\Dto\ApiResponse;
use App\Dto\Request\NewPassRequest;
use App\Dto\Response\Transformers\UserDtoTransformer;
use App\Entity\PassToken;
use App\Repository\PassTokenRepository;
use App\Repository\UserRepository;
use App\Service\EmailService;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Phpro\ApiProblem\Exception\ApiProblemException;
use Phpro\ApiProblem\Http\BadRequestProblem;
use Phpro\ApiProblemBundle\Exception\ApiProblemHttpException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Security(properties: [])]
#[Route('/api/reset', name: 'api_reset_')]
class ApiResetController extends AbstractController
{

    public function __construct(
        private UserDtoTransformer  $dtoTransformer,
        private JsonResponseFactory $jsonResponse,
        private LoggerInterface     $logger,
        private EmailService        $emailService
    )
    {
    }

    /**
     * @throws \Exception
     */
    #[OA\Tag('PasswordReset')]
    #[Route('/request', name: 'request', methods: 'POST')]
    public function requestReset(
        Request             $request,
        UserRepository      $userRepository,
        PassTokenRepository $passTokenRepository,
        ManagerRegistry     $doctrine,
        TransportInterface  $mailer
    ): JsonResponse
    {
        if (!$request->get("email")) {
            throw new BadRequestException("invalid email");
        }
        $email = $request->get("email");
        $user = $userRepository->findOneBy(["email" => $email]);
        if (!$user) {
            throw new ApiProblemHttpException(new BadRequestProblem("invalid email"));
        }
        $em = $doctrine->getManager();

        $bytes = random_bytes(3);
        $code = strtoupper(bin2hex($bytes));

        $passToken = new PassToken();
        $passToken->setToken($code);
        $passToken->setIdUser($user);

        $em->persist($passToken);
        $em->flush();

        $this->emailService->sendTemplatedEmail(
            $user->getEmail(),
            [
                'CODE' => $code

            ],
            2);
        return $this->jsonResponse->create(new ApiResponse("Email send successfully", null));

    }

    /**
     * @throws ApiProblemException
     */
    #[OA\Tag('PasswordReset')]
    #[Route('/verify', name: 'verify', methods: 'POST')]
    public function verifyReset(
        Request             $request,
        UserRepository      $userRepository,
        PassTokenRepository $passTokenRepository,
        ManagerRegistry     $doctrine
    ): JsonResponse
    {
        $em = $doctrine->getManager();
        if (!$request->get("email") || !$request->get("code")) {
            throw new ApiProblemException(new BadRequestProblem("invalid data"));
        }
        $email = $request->get("email");
        $code = $request->get("code");
        $user = $userRepository->findOneBy(["email" => $email]);
        if (!$user) {
            throw new ApiProblemException(new BadRequestProblem("invalid email"));
        }
        $tokenPass = $passTokenRepository->findOneBy(["idUser" => $user->getId(), "token" => $code, "used" => false]);
        if ($tokenPass) {
            $tokenPass->setUsed(true);
            $em->flush();
            return $this->jsonResponse->create(new ApiResponse("code found", null));

        } else {
            return $this->jsonResponse->create(new ApiResponse("code not found", null, 404));

        }
    }

    /**
     * @throws ApiProblemException
     */
    #[OA\Tag('PasswordReset')]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: NewPassRequest::class)
    )]
    #[Route('/confirm', name: 'confirm', methods: 'POST')]
    public function confirmReset(
        NewPassRequest              $request,
        UserRepository              $userRepository,
        ManagerRegistry             $doctrine,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        $em = $doctrine->getManager();

        $email = $request->email;
        $user = $userRepository->findOneBy(["email" => $email]);
        if (!$user) {
            throw new ApiProblemException(new BadRequestProblem("invalid email"));
        }
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $request->newpass
        );
        $user->setPassword($hashedPassword);
        $em->flush();

        return $this->jsonResponse->create(new ApiResponse("pass reset success", null));

    }
}