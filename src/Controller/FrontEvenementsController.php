<?php

namespace App\Controller;

use App\Repository\EvenementRepository;
use App\Repository\SponsorRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\Writer\PngWriter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route(name: 'app_front_evenements_')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class FrontEvenementsController extends AbstractController
{
    #[Route('/evenements', name: 'index')]
    public function afficherEvenementsFront(EvenementRepository $EvenementRepository, SponsorRepository $sponsorRepository): Response
    {
        if ($this->getUser()->getAbonnement() === null || !in_array($this->getUser()->getAbonnement()->getIdOffre()->getType(), ["Silver", "Gold"])) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        return $this->render('front_evenements/index.html.twig', [
            'evenements' => $EvenementRepository->findAll(),
            'sponsors' => $sponsorRepository->findAll(),
        ]);
    }


    #[Route('/evenements/{id}', name: 'details')]
    public function voirdetails(EvenementRepository $EvenementRepository, $id): Response
    {
        if ($this->getUser()->getAbonnement() === null || !in_array($this->getUser()->getAbonnement()->getIdOffre()->getType(), ["Silver", "Gold"])) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        $evenement = $EvenementRepository->find($id);
        return $this->render('front_evenements/details.html.twig', [
            'evenement' => $evenement,
        ]);


    }


    #[Route('/evenements/participer/{id}', name: 'participer')]
    public function ParticiperEvenement(EvenementRepository $evenementRepository, int $id, UserRepository $userRepository, ManagerRegistry $doctrine): Response
    {
        if ($this->getUser()->getAbonnement() === null || !in_array($this->getUser()->getAbonnement()->getIdOffre()->getType(), ["Silver", "Gold"])) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        $user = $this->getUser();
        $evenement = $evenementRepository->find($id);

        if (!$evenement) {
            throw $this->createNotFoundException('Evenement n est pas trouvé');
        }

        if ($evenement->conatinsIdUser($user)) {
            $message = 'Vous êtes déjà inscrit à cet événement.';
            $evenement->setNbParticipants($evenement->getNbParticipants() - 1);
            $evenement->removeIdUser($user);


        } else {
            $evenement->setNbParticipants($evenement->getNbParticipants() + 1);
            $evenement->addIdUser($user);
            $message = 'Inscription réussie à l\'événement.';


        }

        $em = $doctrine->getManager();
        $em->persist($evenement);
        $em->flush();

        $this->addFlash(
            "success",
            $message
        );


        return $this->redirectToRoute('app_front_evenements_details', [
            'message' => $message,
            'id' => $evenement->getId()
        ]);


    }

    #[Route('/qr/{id}', name: 'qr')]
    public function qr($id, EvenementRepository $rep)
    {
        if ($this->getUser()->getAbonnement() === null || !in_array($this->getUser()->getAbonnement()->getIdOffre()->getType(), ["Silver", "Gold"])) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        $dd = $rep->find($id);
        if (!$dd) {
            throw $this->createNotFoundException('Destination not found');
        }

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data("Date Poster :" . $dd->getPoster())
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->labelText("")
            ->labelAlignment(new LabelAlignmentCenter())
            ->labelMargin(new Margin(15, 5, 5, 5))
            ->build();


        $desktopDirectory = 'qr_code_images';

        $namePng = uniqid('', true) . '.png';
        $result->saveToFile($desktopDirectory . $namePng);

        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $namePng . '"');
        $response->setContent(file_get_contents($desktopDirectory . $namePng));
        return $response;
    }


}
