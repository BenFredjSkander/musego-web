<?php

namespace App\Controller\API;

use App\Dto\ApiResponse;
use App\Dto\Response\Transformers\OeuvreDtoTransformer;
use App\Entity\Oeuvre;
use App\Repository\OeuvreRepository;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/oeuvres', name: 'api_')]
class ApiOeuvresController extends AbstractController
{
    public function __construct(private readonly JsonResponseFactory $factory, private readonly OeuvreDtoTransformer $oeuvreDtoTransformer)
    {
    }

    #[OA\Tag('Oeuvres')]
    #[Route('/list', name: 'app_api_oeuvres', methods: ['GET'])]
    public function index(OeuvreRepository $repo): JsonResponse
    {
        $oeuvres = $repo->findAll();
        $data = $this->oeuvreDtoTransformer->transformFromObjects($oeuvres);


        return $this->factory->create(new ApiResponse('oeuvres Successfully Retrieved', $data));
    }

    #[OA\Tag('Oeuvres')]
    #[Route('/categorie/{id}', name: 'categorie', methods: ['GET'])]
    public function showOeuvre(OeuvreRepository $repo, int $id): Response
    {
        //Trouver la bonne catégorie
        //    $repoC = $doctrine->getRepository(Oeuvre::class);
        $oeuvres = $repo->findBy(['idCategorie' => $id]);

        // Vérifier si la catégorie existe
        if (!$oeuvres) {
            throw $this->createNotFoundException('Cette catégorie n\'existe pas.');
        }

        $oeuvreDto = $this->oeuvreDtoTransformer->transformFromObjects($oeuvres);
        // Créer la réponse API
        $apiResponse = new ApiResponse('Catégorie récupérée avec succès', $oeuvreDto);
        $response = $this->factory->create($apiResponse, 200);

        return $response;
    }

    #[OA\Tag('Oeuvres')]
    #[Route('/qr/{id}', name: 'showDescriptionQR', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show($id): JsonResponse
    {
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

        // Supprimer le fichier temporaire après avoir renvoyé la réponse
        $response->deleteFileAfterSend(true);

        // Créer la réponse JSON
        $data = [
            'description' => $oeuvre->getDescription(),
            'qr_code' => base64_encode($qrcode),
        ];
        return new JsonResponse($data);
    }


}