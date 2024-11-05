<?php

namespace App\Controller;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;
use Endroid\QrCode\Label\Font\NotoSans;
use Doctrine\Persistence\ManagerRegistry;
use Swift_Message;
use Swift_Mailer;
use Swift_SmtpTransport;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\AvisRepository;
use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;



#[Route('/back/avis', name: 'app_back_avis_')]
class BackAvisController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->render('back_avis/index.html.twig', [
            'controller_name' => 'BackAvisController',
        ]);
    }



    #[Route('/back', name: 'app_avis_indexBack', methods: ['GET'])]
    public function indexB(Request $request,AvisRepository $avisRepository,PaginatorInterface $paginator): Response
    {
        $query = $avisRepository->createQueryBuilder('p')
        ->orderBy('p.id', 'ASC')
        ->getQuery();
            

            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                2
            );
        return $this->render('avis/backAvis.html.twig', [
            'avis' => $pagination,
            
        ]);
    }


    #[Route('/newB', name: 'app_avis_newB', methods: ['GET', 'POST'])]
    public function newB(Request $request, AvisRepository $avisRepository): Response
    {
        $avi = new Avis();
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avisRepository->save($avi, true);

            return $this->redirectToRoute('app_avis_indexBack', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avis/newBack.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }

    #[Route('/back/{id}', name: 'app_avis_showB', methods: ['GET', 'POST'])]
    public function showB(Request $request,Avis $avi,ReponseRepository $reponseRepository): Response
    {
        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);
       
        $reponses = $reponseRepository->findAll();
        if ($form->isSubmitted() && $form->isValid()) {
            $reponse->setAvisId($avi->getId());
            $reponseRepository->save($reponse, true);

            return $this->renderForm('avis/showB.html.twig', [
                'avi' => $avi,
                'reponses' => $reponses,
                'form' => $form,
                
            ]);
        }
        return $this->renderForm('avis/showB.html.twig', [
            'avi' => $avi,
            'reponses' => $reponses,
            'form' => $form,
            
        ]);
        
        
    }

    #[Route('/back/{id}/edit', name: 'app_avis_editB', methods: ['GET', 'POST'])]
    public function editB(Request $request, Avis $avi, AvisRepository $avisRepository): Response
    {
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avisRepository->save($avi, true);

            return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avis/editB.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }














}
