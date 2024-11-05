<?php

namespace App\Security;

use App\Entity\Session;
use App\Repository\SessionRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class CustomApiSuccessAuthHandler extends AuthenticationSuccessHandler
{
    public function __construct(
        private LoggerInterface   $logger,
        private SessionRepository $sessionRepository,
        JWTTokenManagerInterface  $jwtManager,
        EventDispatcherInterface  $dispatcher, $cookieProviders = [], bool $removeTokenFromBodyWhenCookiesUsed = true)
    {
        parent::__construct($jwtManager, $dispatcher, $cookieProviders, $removeTokenFromBodyWhenCookiesUsed);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $parameters = json_decode($request->getContent(), true);
        $code = $parameters['code'] ?? "0000";
        $this->logger->error("****************");
        $this->logger->error($parameters['code']);


        $session = new Session("Mobile", null, $token->getUser(), $code);
        $result = $this->sessionRepository->findby(['uid' => $code, 'idUser' => $token->getUser()->getId()]);
        if ($result == null) {
//            $this->notifySuspectLogin('+21693934708');
            $this->sessionRepository->save($session);
        }
        return parent::onAuthenticationSuccess($request, $token); // TODO: Change the autogenerated stub
    }


    public function notifySuspectLogin(string $phone): void
    {
        try {
            $sid = $_ENV['TWILIO_SID'];
            $token = $_ENV['TWILIO_TOKEN'];
            $client = new Client($sid, $token);
            $client->messages->create(
                $phone,
                [
                    'from' => '+15673716970',
                    'body' => 'une nouvelle connexion a partir d\'un appareil non reconnu'
                ]
            );
        } catch (ConfigurationException|TwilioException $e) {
            $this->logger->warning("twilio error" . $e);
        }

    }

}