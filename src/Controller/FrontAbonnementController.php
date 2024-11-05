<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Repository\AbonnementRepository;
use App\Repository\OffreRepository;
use App\Service\EmailService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;

#[Route('/abonnements', name: 'app_front_abonnement_')]
class FrontAbonnementController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function index(): Response
    {
        return $this->render('front_abonnement/index.html.twig', [
            'controller_name' => 'FrontAbonnementController',
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/add', name: 'add')]
    public function add(OffreRepository $repooff,AbonnementRepository $repository, ManagerRegistry $doctrine, Request $request,EmailService $emailService): Response
    {
        //put the parameters in a request body
        $id = $request->query->get('id');
        $type = $request->query->get('type');
        $prix = $request->query->get('prix');
        $abonnement = new Abonnement();

        $offre = $repooff->find($id);
        $issubexist=$repository->findByUser($this->getUser());

        $em = $doctrine->getManager();

        if($issubexist) $em->remove($issubexist);

        $abonnement->setIdOffre($offre);
        $abonnement->setIdUser($this->getUser());
        $abonnement->setDateDebut(new \DateTime('now', new \DateTimeZone('Africa/Tunis')));
        if ($type == 'Hebdomadaire') $abonnement->setDateFin(new \DateTime('now +7 days', new \DateTimeZone('Africa/Tunis')));
        else if ($type == 'Mensuel') $abonnement->setDateFin(new \DateTime('now +30 days', new \DateTimeZone('Africa/Tunis')));
        else $abonnement->setDateFin(new \DateTime('now +365 days', new \DateTimeZone('Africa/Tunis')));

        $abonnement->setPrix($prix);
        $abonnement->setType($type);

        $em->persist($abonnement);
        $emailService->sendTemplatedEmail(
            $this->getUser()->getEmail(),
            [
                'FIRSTNAME' => $this->getUser()->getUsername(),
                'TYPE_OFFRE' => $offre->getType(),
                'PRICE_ABONN' => $abonnement->getPrix()
            ],
            4);
//        $emailService->sendTextEmail($this->getUser()->getEmail(), 'Bonjour ' . $this->getUser()->getUsername() . ' Votre abonnement: ' . $offre->getType() . ' est confirmé!. Le montant de ' . $abonnement->getPrix() . '€ a été déduit de votre carte avec la date du: ' . date('Y-m-d'));
        $em->flush();

        notyf()->addSuccess('Paiement effectué avec succés!');

        return $this->redirectToRoute('app_front_profile_index');
    }

    #[Route('/pdf/{id}', name: 'pdf')]
    public function genPDF(Abonnement $abonnement): Response
    {

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 25);


        $pdf->Cell(0, 10, 'MuseGO', 0, 1, 'C');


        $pdf->SetFont('helvetica', 'B', 20);

        $pdf->Cell(0, 10, 'Facturation de l\'abonnement ' . $abonnement->getType(), 0, 1, 'C');

        //$pdf->Image('images/musego.png', $x=150, $y=10, $w=40, $h=20, 'PNG');
        $pdf->SetFont('helvetica', '', 12);


        $pdf->Cell(0, 10, 'Cette facture est destiné au client titulaire du nom: ' . $this->getUser()->getUsername(), 0, 1, 'C');
        $pdf->Cell(0, 10, 'ayant effectué l\'ordre de paiement avec les informations correspondantes: ', 0, 1, 'C');

        $pdf->Cell(0, 10, 'Type d\'abonnement : ' . $abonnement->getType(), 0, 1);
        $pdf->Cell(0, 10, 'Sous l\'offre : ' . $abonnement->getIdOffre()->getType(), 0, 1);
        $pdf->Cell(0, 10, 'Date de début : ' . $abonnement->getDateDebut()->format('d/m/Y'), 0, 1);
        $pdf->Cell(0, 10, 'Date de fin : ' . $abonnement->getDateFin()->format('d/m/Y'), 0, 1);
        $pdf->Cell(0, 10, 'Prix : ' . $abonnement->getPrix() . '€', 0, 1);
        $pdf->Cell(0, 10, 'Avec une promotion : ' . $abonnement->getIdOffre()->getPromotion() . '%', 0, 1);
        $pdf->Cell(0, 10, 'Description de l\'offre : ' . $abonnement->getIdOffre()->getDescription(), 0, 1);



        $pdf->Cell(0, 10, 'Créé le : ' . date_create()->format('Y-m-d') , 0, 1,'R');

        $content = $pdf->Output('Facture ' . $abonnement->getType() . '.pdf', 'S');

        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="Facture Abonnement ' . $abonnement->getType() . ' MuseGO.pdf"');

        return $response;
    }
}
