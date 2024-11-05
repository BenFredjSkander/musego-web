<?php

namespace App\Controller;


use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use App\Service\EmailService;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use TCPDF;


#[Route('/back/evenements', name: 'app_back_')]
class BackEvenementsController extends AbstractController
{


    public function __construct(private LoggerInterface $logger, private SerializerInterface $serializer)
    {

    }


    #[Route(name: 'evenements')]
    public function afficherEvenements(EvenementRepository $EvenementRepository): Response
    {
        $data = $EvenementRepository->findAll();
        return $this->render('back_evenements/liste_evenements_back.html.twig', [
            'evenements' => $data
        ]);


    }


    #[Route('/add', name: 'evenements_add')]
    public function ajouterEvenements(ManagerRegistry $doctrine, Request $request, UploaderService $fileUploader): Response
    {

        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brochureFile = $form->get('poster')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $evenement->setPoster($brochureFileName);
            }


            $em = $doctrine->getManager();
            $em->persist($evenement);
            $em->flush();

            return $this->redirectToRoute('app_back_evenements');
        }

        return $this->render('back_evenements/ajouter_evenement_back.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);

    }


    #[Route('/remove/{id}', name: 'evenements_remove')]
    public function SupprimerEvenements(ManagerRegistry $doctrine, $id, EmailService $mailer): Response
    {
        $em = $doctrine->getManager();
        $evenement = $em->getRepository(Evenement::class)->find($id);

        if (!$evenement) {
            throw $this->createNotFoundException('L\'événement avec l\'id ' . $id . ' n\'existe pas.');
        }


        $emails = array();
        $participants = $evenement->getIdUser();

        foreach ($participants as $participant) {
            $emails[] = $participant->getEmail();
        }


        $DATEEVENT = $evenement->getDateDebut();
        $timestamp = $DATEEVENT->getTimestamp();

        $dateString = date('Y-m-d H:i:s', $timestamp);
        $mailer->sendTextEmail('contact.musego@gmail.com', 'L\'evenement ' . $evenement->getNom() . ' a ete annulé, merci');


        $em->remove($evenement);
        $em->flush();


        return $this->redirectToRoute('app_back_evenements');
    }


    #[Route('/update/{id}', name: 'evenements_update')]
    public function modifierEvenements(Request $request, ManagerRegistry $doctrine, Evenement $evenement)
    {
        $form = $this->createForm(EvenementType::class, $evenement);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('app_back_evenements');
        }
        return $this->render('back_evenements/modifier_evenement_back.html.twig', [
            'form' => $form->createView(),
        ]);

    }


    #[Route('/voir_rapport/{id}', name: 'evenements_voir_rapport')]
    public function VoirRapport(Evenement $evenement): Response
    {

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 20);

        $pdf->Cell(0, 10, 'Rapport de l\'événement ' . $evenement->getNom(), 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 12);

        // Ajouter les informations de l'événement
        $pdf->Cell(0, 10, 'Date de début : ' . $evenement->getDateDebut()->format('d/m/Y'), 0, 1);
        $pdf->Cell(0, 10, 'Date de fin : ' . $evenement->getDateFin()->format('d/m/Y'), 0, 1);
        $pdf->Cell(0, 10, 'Lieu : ' . $evenement->getLieu(), 0, 1);
        $pdf->Cell(0, 10, 'Type : ' . $evenement->getType(), 0, 1);
        $pdf->Cell(0, 10, 'Description : ' . $evenement->getDescription(), 0, 1);
        $pdf->Cell(0, 10, 'Nombre de participants : ' . $evenement->getNbParticipants(), 0, 1);

        $content = $pdf->Output('Rapport ' . $evenement->getNom() . '.pdf', 'S');


        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="Rapport ' . $evenement->getNom() . '.pdf"');

        return $response;
    }


    #[Route('/stats', name: 'evenements_stats')]
    public function stats(EvenementRepository $repository): Response
    {

        $evenements = $repository->findAll();

        $dataParticipants = array();

        foreach ($evenements as $evenement) {
            $dataParticipants[$evenement->getNom()] = $evenement->getNbParticipants();


        }
        return $this->render('back_evenements/stats_events.html.twig', [
            'data_participation' => json_encode($dataParticipants)
        ]);
    }


    /**
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    #[Route('/search', name: 'evenementssearch')]
    public function afficherEvenementsSearch(Request $request, EvenementRepository $sr): Response
    {
        $requestString = $request->get('searchValue');
        $evenements = $sr->findByNom($requestString);


        $jsonContent = $this->serializer->serialize($evenements, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }]);


        return new Response($jsonContent);

    }


    #[Route('/tri', name: 'evenementstri')]
    public function afficherEvenementsTri(Request $request, EvenementRepository $sr): Response
    {
        $evenements = [];

        $triPar = $request->get('triPar');

        if ($triPar === 'date_debut') {
            $evenements = $sr->triByDateDebut();
        } elseif ($triPar === 'nom') {
            $evenements = $sr->triByNom();
        } elseif ($triPar === 'nb_participants') {
            $evenements = $sr->triByNbParticipants();
        }

        $jsonContent = $this->serializer->serialize($evenements, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }]);


        $this->logger->alert($jsonContent);

        return new Response($jsonContent);

    }


}
