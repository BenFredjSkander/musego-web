<?php

namespace App\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontHomeController extends AbstractController
{
    #[Route('/', name: 'app_front_home')]
    public function index(Request $request): Response
    {
        $form = $this->createFormBuilder(null, [
            'attr' => [
                'name' => 'touch-form',
                'id' => 'touch-form',
                'class' => 'default-form'
            ]
        ])
            ->add('name', TextType::class, ['attr' => ['placeholder' => 'Nom']])
            ->add('email', EmailType::class, ['attr' => ['placeholder' => 'Email']])
            ->add('message', TextareaType::class, ['attr' => ['placeholder' => 'Votre message']])
            ->add('send', SubmitType::class, ['label' => 'Envoyer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();
            printf('hello***************************');
            $this->addFlash(
                "success",
                "Votre message est bien envoyé."
            );
            return $this->redirectToRoute('app_front_home');

        }
        return $this->render('front_home/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws GuzzleException
     */
    #[Route('/subscribenews', name: 'app_front_home_subscribenews')]
    public function subscribeNew(Request $request): Response
    {
        $apiKey = $this->getParameter('app.sendinblueapi');
        $email = $request->request->get('email');
        $segment = '';
        if ($request->request->has('newsType1') && $request->request->has('newsType2')) {
            $segment = 'ALL';
        } elseif ($request->request->has('newsType1') && !$request->request->has('newsType2')) {
            $segment = 'EVENETS';
        } else {
            $segment = 'WORKSHOPS';
        }

        $client = new Client();

        $client->request('POST', 'https://api.sendinblue.com/v3/contacts', [
            'body' => '{"attributes":{"NEWSTYPE":"' . $segment . '"},"updateEnabled":true,"email":"' . $email . '"}',
            'headers' => [
                'accept' => 'application/json',
                'api-key' => $apiKey,
                'content-type' => 'application/json',
            ],
        ]);
        return new Response(null, Response::HTTP_CREATED);
    }
}
