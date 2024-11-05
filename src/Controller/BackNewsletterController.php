<?php

namespace App\Controller;

use App\Dto\RootContacts;
use Doctrine\Persistence\ManagerRegistry;
use Dukecity\CommandSchedulerBundle\Entity\ScheduledCommand;
use Dukecity\CommandSchedulerBundle\Form\Type\ScheduledCommandType;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface as ContractsTranslatorInterface;

#[Route('/back/newsletter', name: 'app_back_newsletter_')]
class BackNewsletterController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine, private ContractsTranslatorInterface $translator)
    {
    }

    private int $lockTimeout = 3600;
    private LoggerInterface $logger;


    public function setLockTimeout(int $lockTimeout): void
    {
        $this->lockTimeout = $lockTimeout;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    protected array $encoder;
    protected array $normalizer;
    protected Serializer $serializer;


    /**
     * @throws GuzzleException
     */
    #[Route('/contacts', name: 'contacts')]
    public function contacts(): Response
    {
        $this->encoder = [new JsonEncoder()];
        $this->normalizer = [new ObjectNormalizer()];
        $this->serializer = new Serializer($this->normalizer, $this->encoder);
        $client = new Client();

        $apiKey = $this->getParameter('app.sendinblueapi');

        $response = $client->request('GET', 'https://api.sendinblue.com/v3/contacts', [
            'headers' => [
                'accept' => 'application/json',
                'api-key' => $apiKey,
            ],
        ]);

        $contacts = $this->serializer->deserialize($response->getBody()->getContents(), RootContacts::class, 'json');
        return $this->render('back_newsletter/contacts.html.twig', [
            'contacts' => $contacts->contacts
        ]);
    }

    /**
     * @throws GuzzleException
     */
    #[Route('/contacts/delete/{email}', name: 'contacts_delete')]
    public function contactsDelete($email): Response
    {
        $client = new Client();

        $apiKey = $this->getParameter('app.sendinblueapi');

        $client->request('DELETE', 'https://api.sendinblue.com/v3/contacts/' . $email, [
            'headers' => [
                'accept' => 'application/json',
                'api-key' => $apiKey,
            ],
        ]);

        return $this->redirectToRoute('app_back_newsletter_contacts');
    }

    #[Route('/command/list', name: 'command_scheduler_list')]
    public function indexAction(): Response
    {
        $scheduledCommands = $this->doctrine->getRepository(
            ScheduledCommand::class
        )->findAll();
        #)->findAllSortedByNextRuntime();

        return $this->render(
            'back_newsletter/commands_list.html.twig',
            ['scheduledCommands' => $scheduledCommands]
        );
    }

    #[Route('/command/remove/{id}', name: 'command_scheduler_action_remove')]
    public function removeAction($id): RedirectResponse
    {
        $entityManager = $this->doctrine;
        $scheduledCommand = $entityManager->getRepository(ScheduledCommand::class)->find($id);
        $entityManager->getManager()->remove($scheduledCommand);
        $entityManager->getManager()->flush();

        // Add a flash message and do a redirect to the list
        $this->addFlash('success', $this->translator->trans('flash.deleted', [], 'DukecityCommandScheduler'));

        return $this->redirectToRoute('app_back_newsletter_command_scheduler_list');
    }

    #[Route('/command/toggle/{id}', name: 'command_scheduler_action_toggle')]
    public function toggleAction($id): RedirectResponse
    {
        $scheduledCommand = $this->doctrine->getRepository(ScheduledCommand::class)->find($id);
        $scheduledCommand->setDisabled(!$scheduledCommand->isDisabled());
        $this->doctrine->getManager()->flush();

        return $this->redirectToRoute('app_back_newsletter_command_scheduler_list');
    }

    #[Route('/command/execute/{id}', name: 'command_scheduler_action_execute')]
    public function executeAction($id, Request $request): RedirectResponse
    {
        $scheduledCommand = $this->doctrine->getRepository(ScheduledCommand::class)->find($id);
        $scheduledCommand->setExecuteImmediately(true);
        $this->doctrine->getManager()->flush();

        // Add a flash message and do a redirect to the list
        $this->addFlash('success', $this->translator->trans('flash.execute', ["%name%" => $scheduledCommand->getName()], 'DukecityCommandScheduler'));

        if ($request->query->has('referer')) {
            return $this->redirect($request->getSchemeAndHttpHost() . urldecode($request->query->get('referer')));
        }

        return $this->redirectToRoute('app_back_newsletter_command_scheduler_list');
    }

    #[Route('/command/unlock/{id}', name: 'command_scheduler_action_unlock')]
    public function unlockAction($id, Request $request): RedirectResponse
    {
        $scheduledCommand = $this->doctrine->getRepository(ScheduledCommand::class)->find($id);
        $scheduledCommand->setLocked(false);
        $this->doctrine->getManager()->flush();

        // Add a flash message and do a redirect to the list
        $this->addFlash('success', $this->translator->trans('flash.unlocked', [], 'DukecityCommandScheduler'));

        if ($request->query->has('referer')) {
            return $this->redirect($request->getSchemeAndHttpHost() . urldecode($request->query->get('referer')));
        }

        return $this->redirectToRoute('app_back_newsletter_command_scheduler_list');
    }


    /**
     * Handle display of new/existing ScheduledCommand object.
     */
    #[Route('/command/details/{id}', name: 'command_scheduler_details_edit')]
    #[Route('/command/details', name: 'command_scheduler_details_new')]
    public function edit(Request $request, $id = null): Response
    {
        $scheduledCommand = $id ? $this->doctrine->getRepository(ScheduledCommand::class)->find($id) : null;
        if (!$scheduledCommand) {
            $scheduledCommand = new ScheduledCommand();
        }

        $form = $this->createForm(ScheduledCommandType::class, $scheduledCommand);
        $form->remove('save');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // check if we have an xml-read error for commands
            if ('error' === $scheduledCommand->getCommand()) {
                $this->addFlash('error', 'ERROR: please check php bin/console list --format=xml');

                return $this->redirectToRoute('app_back_newsletter_command_scheduler_list');
            }

            $em = $this->doctrine->getManager();
            $em->persist($scheduledCommand);
            $em->flush();

            // Add a flash message and do a redirect to the list
            $this->addFlash('success', $this->translator->trans('flash.success', [], 'DukecityCommandScheduler'));

            return $this->redirectToRoute('app_back_newsletter_command_scheduler_list');
        }

        return $this->render(
            'back_newsletter/command_details.html.twig',
            ['scheduledCommandForm' => $form->createView()]
        );
    }

}
