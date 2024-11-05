<?php

namespace App\Security;

use App\Entity\Session;
use App\Repository\SessionRepository;
use donatj\UserAgent\UserAgentParser;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class AppWebAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private ValidatorInterface $validator;

    public function __construct(private UrlGeneratorInterface $urlGenerator,
                                ValidatorInterface            $validator,
                                private SessionRepository     $sessionRepository,
                                private LoggerInterface       $logger
    )
    {
        $this->validator = $validator;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');
        $this->validateCredentials($request);
        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    /**
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $parser = new UserAgentParser();
        $ua = $parser->parse();
        $session = new Session($ua->platform(), $ua->browser(), $token->getUser(), $request->request->get('fprint'));
        $result = $this->sessionRepository->findby(['uid' => $request->request->get('fprint'), 'idUser' => $token->getUser()->getId()]);
        if ($result == null) {
//            $this->notifySuspectLogin('+21693934708');
            $this->sessionRepository->save($session);
        }
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:
        return new RedirectResponse($this->urlGenerator->generate('app_front_home'));
//        throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    public function validateCredentials(Request $credentials): ConstraintViolationListInterface
    {
        $constraints = new Assert\Collection([
            'fields' => [
                'email' => [
                    new NotBlank([
                        'message' => 'The fields Email are missing'
                    ]),
                    new Email([
                        'message' => 'Please enter a valid email address.'
                    ])
                ],
                'password' => [
                    new NotBlank(['message' => 'The fields Password are missing.',]),
                ]
            ],
            'allowExtraFields' => true
        ]);
        $errors = $this->validator->validate(
            $credentials->request->all(),
            $constraints
        );
        $arrErrors = [];

        if (0 !== $errors->count()) {
            for ($i = 0; $i < $errors->count(); $i++) {
                $arrErrors += [trim($errors->get($i)->getPropertyPath(), '[]') => $errors->get($i)->getMessage()];
            }
        }

        if (0 !== $errors->count()) {
            $credentials->getSession()->set('login-errors', $arrErrors);
        } else {
            $credentials->getSession()->remove('login-errors');
        }

        return $errors;
    }

    /**
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function notifySuspectLogin(string $phone): void
    {
        try {
            $sid = $_ENV['TWILIO_SID'];;
            $token = $_ENV['TWILIO_TOKEN'];;
            $client = new Client($sid, $token);
            $client->messages->create($phone, ['from' => '+15673716970', 'body' => 'une nouvelle connexion a partir d\'un appareil non reconnu']);
        } catch (ConfigurationException|TwilioException $e) {
            $this->logger->warning("twilio error" . $e);
        }

    }

}
