<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Oeuvre;
use App\Repository\OeuvreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/oeuvres', name: 'app_front_oeuvre_')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class   FrontOeuvreController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    //Affichage d'1 seul:
    #[Route('/categorie/{id}', name: 'categorie')]
    public function index(OeuvreRepository $repo, int $id): Response
    {
        if ($this->getUser()->getAbonnement() === null) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        $oeuvres = $repo->findBy(['idCategorie' => $id]);

        return $this->render('front_oeuvre/index.html.twig', [
            'oeuvres' => $oeuvres,
        ]);
    }


    #[Route('/qr/{id}', name: 'showDescriptionQR', requirements: ['id' => '\d+'])]
    public function show($id): Response
    {
        if ($this->getUser()->getAbonnement() === null) {
            return $this->redirectToRoute('app_front_offre_list');
        }
        $repository = $this->getDoctrine()->getRepository(Oeuvre::class);
        $oeuvre = $repository->find($id);

        if (!$oeuvre) {
            throw $this->createNotFoundException('L\'oeuvre correspondante n\'a pas été trouvée.');
        }

        $writer = new PngWriter();
        $qrCode = QrCode::create($oeuvre->getDescription())
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(700)
            ->setMargin(0);

        $qrcode = $writer->write($qrCode)->getString();

        // Enregistrer le code QR généré dans un fichier temporaire
        $filename = tempnam(sys_get_temp_dir(), 'qr_');
        file_put_contents($filename, $qrcode);

        // Créer une réponse avec le fichier temporaire
        $response = new BinaryFileResponse($filename);

        // Ajouter les en-têtes de réponse pour forcer le téléchargement du fichier
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'qr_code.png');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }


    #[Route('/tri-oeuvresParCateg/{id}', name: 'tri_oeuvres')]
    public function trierParCategorie(Request $request, OeuvreRepository $oeuvreRepository, int $id): Response
    {
        if ($this->getUser()->getAbonnement() === null) {
            return $this->redirectToRoute('app_front_offre_list');
        }

        $categorieRepository = $this->entityManager->getRepository(Categorie::class);
        $categorie = $categorieRepository->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException('La catégorie demandée n\'existe pas');
        }

        $oeuvres = $oeuvreRepository->trierParCategorieEtTitre($categorie, 'titre', 'ASC');

        return $this->render('front_oeuvre/index.html.twig', [
            'oeuvres' => $oeuvres,
            'categorie' => $categorie,
        ]);
    }


}
