<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\AvisRepository;
use App\Repository\ReponseRepository;
use App\Service\EmailService;
use Doctrine\Persistence\ManagerRegistry;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/avis', name: 'app_front_avis_')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class FrontAvisController extends AbstractController
{
    public function __construct(private EmailService $emailService)
    {
    }

    #[Route('', name: 'app_avis_index', methods: ['GET'])]
    public function index(AvisRepository $avisRepository, Request $request): Response
    {
        if ($this->getUser()->getAbonnement() === null) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $avis = $avisRepository->findBySearch($searchData);

            return $this->render('avis/index.html.twig', [
                'form' => $form->createView(),
                'avis' => $avis,
            ]);

        }
        return $this->render('avis/index.html.twig', [
            'form' => $form->createView(),
            'avis' => $avisRepository->findBy(['idUser' => $this->getUser()->getId()]),
        ]);
    }

    #[Route('/trier', name: 'app_avis_trier', methods: ['GET'])]
    public function trie(AvisRepository $avisRepository, Request $request): Response
    {
        if ($this->getUser()->getAbonnement() === null) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $avis = $avisRepository->findBySearch($searchData);

            return $this->render('avis/index.html.twig', [
                'form' => $form->createView(),
                'avis' => $avis,
            ]);

        }
        return $this->render('avis/index.html.twig', [
            'form' => $form->createView(),
            'avis' => $avisRepository->trier(),
        ]);
    }

    #[Route('/QrCode', name: 'app_qr_codes')]
    public function qrGenerator(ManagerRegistry $doctrine)
    {
        if ($this->getUser()->getAbonnement() === null) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        //$ref = $doctrine->getManager();
        $ref = ("http://localhost/pidev/public/index.php/avis");
        $qrcode = QrCode::create($ref)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
        $writer = new PngWriter();
        return new Response($writer->write($qrcode)->getString(),
            Response::HTTP_OK,
            ['content-type' => 'image/png']
        );


    }


    #[Route('/new', name: 'app_avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AvisRepository $avisRepository): Response
    {
        if ($this->getUser()->getAbonnement() === null) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        $avi = new Avis();
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avi->setIdUser($this->getUser());
            $avisRepository->save($avi, true);
            $this->emailService->sendTextEmail($this->getUser()->getEmail(), 'Merci pour votre avis, Nous vous contacterons dans les plus brefs délais');

            return $this->redirectToRoute('app_front_avis_app_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avis/new.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_avis_show', methods: ['GET'])]
    public function show(Avis $avi, ReponseRepository $reponseRepository): Response
    {
        if ($this->getUser()->getAbonnement() === null) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        $reponses = $reponseRepository->findAll();
        return $this->render('avis/show.html.twig', [
            'avi' => $avi,
            'reponses' => $reponses,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_avis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avis $avi, AvisRepository $avisRepository): Response
    {
        if ($this->getUser()->getAbonnement() === null) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avisRepository->save($avi, true);

            return $this->redirectToRoute('app_front_avis_app_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avis/edit.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_avis_delete', methods: ['POST'])]
    public function delete(Request $request, Avis $avi, AvisRepository $avisRepository): Response
    {
        if ($this->getUser()->getAbonnement() === null) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        if ($this->isCsrfTokenValid('delete' . $avi->getId(), $request->request->get('_token'))) {
            $avisRepository->remove($avi, true);
        }

        return $this->redirectToRoute('app_front_avis_app_avis_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/qrCode/{id}', name: 'app_Qr_Code')]
    public function qrCode(ManagerRegistry $doctrine, $id)
    {
        if ($this->getUser()->getAbonnement() === null) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        return $this->render("avis/QR.html.twig", ['id' => $id]);
    }


}
