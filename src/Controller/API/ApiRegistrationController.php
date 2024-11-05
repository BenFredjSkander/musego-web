<?php

namespace App\Controller\API;

use App\Dto\ApiResponse;
use App\Dto\Request\EmailVerifRequest;
use App\Dto\Request\RegisterRequest;
use App\Entity\EmailVerification;
use App\Entity\User;
use App\Repository\EmailVerificationRepository;
use App\Repository\UserRepository;
use App\Service\EmailService;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Phpro\ApiProblem\Exception\ApiProblemException;
use Phpro\ApiProblem\Http\BadRequestProblem;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Security(properties: [])]
#[Route('/api/register', name: 'api_')]
class ApiRegistrationController extends AbstractController
{
    public function __construct(private JsonResponseFactory $jsonResponse, private LoggerInterface $logger)
    {

    }

    /**
     * Register new user
     * @throws \Exception
     */
    #[OA\Response(
        response: 201,
        description: 'Returns the success response',
//        content: new Model(
//            properties: [new Property('data', type: "int")], type: ApiResponse::class
//        )
    )]
    #[OA\Response(response: 500, description: 'Duplicate email')]
    #[OA\Response(response: 400, description: 'Validation errors')]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: RegisterRequest::class)
    )]
    #[OA\Tag('Authentication', description: 'Register new user')]
    #[Route("", name: 'register', methods: 'POST')]
    public function index(
        ManagerRegistry             $doctrine,
        RegisterRequest             $request,
        UserPasswordHasherInterface $passwordHasher,
        EmailVerificationRepository $verificationRepository,
        TransportInterface          $mailer,
        EmailService                $emailService
    ): Response
    {

        $em = $doctrine->getManager();
        $user = new User();
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $request->password
        );
        $user->setPassword($hashedPassword);
        $user->setEmail($request->email);
        $user->setUsername($request->username);
        $user->setPhoneNumber($request->phone);
        $em->persist($user);
        $em->flush();

        $bytes = random_bytes(3);
        $code = strtoupper(bin2hex($bytes));

        $emailVerif = new EmailVerification();
        $emailVerif->setIdUser($user);
        $emailVerif->setToken($code);
        $em->persist($emailVerif);
        $em->flush();

        $emailService->sendTemplatedEmail(
            $user->getEmail(),
            [
                'CODE' => $code

            ],
            1);

        return $this->jsonResponse->create(new ApiResponse('Registered Successfully, please check your email', null, Response::HTTP_CREATED));
    }

    /**
     * Verify and enable new user account
     * @throws ApiProblemException
     */
    #[OA\Tag('Authentication')]
    #[Security(name: 'null')]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: EmailVerifRequest::class)
    )]
    #[Route('/verify', name: 'verify', methods: 'POST')]
    public function verifyReset(
        EmailVerifRequest           $request,
        UserRepository              $userRepository,
        EmailVerificationRepository $emailVerifRepository,
        ManagerRegistry             $doctrine
    ): JsonResponse
    {
        $em = $doctrine->getManager();

        $user = $userRepository->findOneBy(["email" => $request->email]);
        if (!$user) {
            throw new ApiProblemException(new BadRequestProblem("invalid email"));
        }
        $tokenPass = $emailVerifRepository->findOneBy(
            ["idUser" => $user->getId(), "token" => $request->code, "used" => false]
        );
        if ($tokenPass) {
            $user->setIsVerified(true);
            $tokenPass->setUsed(true);
            $em->flush();
            return $this->jsonResponse->create(new ApiResponse("code found", null));

        } else {
            return $this->jsonResponse->create(new ApiResponse("code not found", null, 404));

        }
    }
}