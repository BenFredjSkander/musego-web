<?php

namespace App\Controller\API;

use App\Dto\ApiResponse;
use App\Dto\Response\Transformers\AbonnementDtoTransformer;
use App\Entity\Abonnement;
use App\Repository\AbonnementRepository;
use App\Repository\OffreRepository;
use App\Service\EmailService;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Attributes as OA;
use Stripe\Charge;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use TCPDF;


#[Route('/api/abonnements', name: 'api_')]
class ApiAbonnementController extends AbstractController
{
    public function __construct(
        private AbonnementDtoTransformer $abonnementDtoTransformer,
        private JsonResponseFactory      $jsonResponseFactory
    )
    {
    }

    #[OA\Tag('Abonnement')]
    #[Route('/get', name: 'abonnements', methods: 'GET')]
    public function getAbonnements(AbonnementRepository $repository): Response
    {
        $abonnements = $repository->findAll();
        $dto = $this->abonnementDtoTransformer->transformFromObjects($abonnements);

        $response = new ApiResponse("abonnements list", $dto);
        return $this->jsonResponseFactory->create($response);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    #[OA\Tag('Abonnement')]
    #[Route('/existAbonnementByUser', name: 'exist', methods: 'GET')]
    public function existAbonnementByUser(UserInterface $user, AbonnementRepository $repository): Response
    {
        $issubexist = $repository->findByUser($user);

        if ($issubexist) {
            $dto = $this->abonnementDtoTransformer->transformFromObject($issubexist);
        } else {
            $dto = null;
        }


        $response = new ApiResponse("abonnement ", $dto);
        return $this->jsonResponseFactory->create($response);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    #[OA\Tag('Abonnement')]
    #[Route('/existAbonnementByUserOffre', name: 'existOffre', methods: 'GET')]
    public function existAbonnementByUserOffre(
        UserInterface        $user,
        Request              $request,
        AbonnementRepository $repository,
        OffreRepository      $offreRepository
    ): Response
    {

        $idoffre = $request->query->get('idoffre');
        $offre = $offreRepository->find($idoffre);

        $count = $repository->existUserandOffre($user, $offre->getId());
        if ($count != 0) {
            $sub = $repository->findByUserandOffre($user, $offre->getId());
            $dto = $this->abonnementDtoTransformer->transformFromObject($sub);

            $response = new ApiResponse("abonnement lié avec offre ", $dto);
            return $this->jsonResponseFactory->create($response);
        }
        $response = new ApiResponse("abonnement lié avec offre ", null);
        return $this->jsonResponseFactory->create($response);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    #[OA\Tag('Abonnement')]
    #[Route('/add', name: 'add', methods: 'POST')]
    public function addAbonnement(
        UserInterface        $user,
        OffreRepository      $repooff,
        AbonnementRepository $repository,
        ManagerRegistry      $doctrine,
        Request              $request,
        EmailService         $emailService
    ): Response
    {
        //put the parameters in a request body
        $id = $request->query->get('idoffre');
        $type = $request->query->get('type');
        $prix = $request->query->get('prix');
        $abonnement = new Abonnement();

        $offre = $repooff->find($id);
        $issubexist = $repository->findByUser($user);

        $em = $doctrine->getManager();

        if ($issubexist) {
            $em->remove($issubexist);
        }

        $abonnement->setIdOffre($offre);
        $abonnement->setIdUser($user);
        $abonnement->setDateDebut(new \DateTime('now', new \DateTimeZone('Africa/Tunis')));
        if ($type == 'Hebdomadaire') {
            $abonnement->setDateFin(new \DateTime('now +7 days', new \DateTimeZone('Africa/Tunis')));
        } else if ($type == 'Mensuel') {
            $abonnement->setDateFin(new \DateTime('now +30 days', new \DateTimeZone('Africa/Tunis')));
        } else {
            $abonnement->setDateFin(new \DateTime('now +365 days', new \DateTimeZone('Africa/Tunis')));
        }

        $abonnement->setPrix($prix);
        $abonnement->setType($type);

        $em->persist($abonnement);
        $emailService->sendTemplatedEmail(
            $user->getEmail(),
            [
                'FIRSTNAME' => $user->getUsername(),
                'TYPE_OFFRE' => $offre->getType(),
                'PRICE_ABONN' => $abonnement->getPrix()
            ],
            4);
        $em->flush();

        $response = new ApiResponse("abonnement add",
            $this->abonnementDtoTransformer->transformFromObject($abonnement)
        );
        return $this->jsonResponseFactory->create($response);
    }

    #[OA\Tag('Abonnement')]
    #[Route('/pdf/{id}', name: 'pdf', methods: 'GET')]
    public function genPDF(UserInterface $user, AbonnementRepository $abonnementRepository, $id): Response
    {
        $abonnement = $abonnementRepository->find($id);

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 25);


        $pdf->Cell(0, 10, 'MuseGO', 0, 1, 'C');


        $pdf->SetFont('helvetica', 'B', 20);

        $pdf->Cell(0, 10, 'Facturation de l\'abonnement ' . $abonnement->getType(), 0, 1, 'C');

        //$pdf->Image('images/musego.png', $x=150, $y=10, $w=40, $h=20, 'PNG');
        $pdf->SetFont('helvetica', '', 12);


        $pdf->Cell(0, 10, 'Cette facture est destiné au client titulaire du nom: ' . $user->getUsername(), 0, 1, 'C');
        $pdf->Cell(0, 10, 'ayant effectué l\'ordre de paiement avec les informations correspondantes: ', 0, 1, 'C');

        $pdf->Cell(0, 10, 'Type d\'abonnement : ' . $abonnement->getType(), 0, 1);
        $pdf->Cell(0, 10, 'Sous l\'offre : ' . $abonnement->getIdOffre()->getType(), 0, 1);
        $pdf->Cell(0, 10, 'Date de début : ' . $abonnement->getDateDebut()->format('d/m/Y'), 0, 1);
        $pdf->Cell(0, 10, 'Date de fin : ' . $abonnement->getDateFin()->format('d/m/Y'), 0, 1);
        $pdf->Cell(0, 10, 'Prix : ' . $abonnement->getPrix() . '€', 0, 1);
        $pdf->Cell(0, 10, 'Avec une promotion : ' . $abonnement->getIdOffre()->getPromotion() . '%', 0, 1);
        $pdf->Cell(0, 10, 'Description de l\'offre : ' . $abonnement->getIdOffre()->getDescription(), 0, 1);


        $pdf->Cell(0, 10, 'Créé le : ' . date_create()->format('Y-m-d'), 0, 1, 'R');

        $content = $pdf->Output('Facture ' . $abonnement->getType() . '.pdf', 'S');

        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="Facture Abonnement ' . $abonnement->getType() . ' MuseGO.pdf"');
        return $response;

    }

    #[OA\Tag('Abonnement')]
    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: 'GET')]
    public function createCharge(UserInterface $user, Request $request)
    {
        $type = $request->query->get('type');
        $prix = $request->query->get('prix');
        $card = $request->query->get('card');


        Stripe::setApiKey($request->query->get('stripeKey'));
        Charge::create([
            //"customer" => $user->getEmail(),
            "amount" => (int)$prix * 100,
            "currency" => "eur",
            "source" => $card,
            "description" => "MuseGO Payment - Abonnement " . $type . " Payé par: " . $user->getUsername()
        ]);

        $response = new ApiResponse("stripe créé", null);
        return $this->jsonResponseFactory->create($response);
    }

    #[OA\Tag('Abonnement')]
    #[Route('/checkout', name: 'checkout', methods: 'GET')]
    public function checkout(Request $request): Response
    {

        $id = $request->query->get('id');
        $type = $request->query->get('type');
        $prix = $request->query->get('prix');

        $apikey = $request->query->get('apikey');
        Stripe::setApiKey($apikey);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $type,
                        ],
                        'unit_amount' => (int)$prix * 100,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_front_abonnement_add', ['id' => $id, 'type' => $type, 'prix' => $prix], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }
}