<?php

namespace App\Controller\API;

use App\Dto\ApiResponse;
use App\Dto\Response\Transformers\EvenementDtoTransformer;
use App\Repository\EvenementRepository;
use Doctrine\Persistence\ManagerRegistry;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


#[Route('/api/evenements', name: 'api_')]
class ApiEvenementscontroller extends AbstractController
{

    public function __construct(
        EvenementDtoTransformer     $evenementDtoTransformer,
        private JsonResponseFactory $jsonResponseFactory
    )
    {
        $this->evenementDtoTransformer = $evenementDtoTransformer;
    }

    #[OA\Tag('Evenement')]
    #[Security(name: 'Bearer')]
    #[Route('', name: 'index', methods: 'GET')]
    public function afficherEvenementsFront(EvenementRepository $EvenementRepository): Response
    {
        $evenements = $EvenementRepository->findAll();
        $evenementDtos = $this->evenementDtoTransformer->transformFromObjects($evenements);

        $response = new ApiResponse("events list", $evenementDtos);
        return $this->jsonResponseFactory->create($response);
    }

    #[OA\Tag('Evenement')]
    #[Route('/{id}', name: 'details', requirements: ['id' => '\d+'], methods: 'GET')]
    public function voirdetails(EvenementRepository $EvenementRepository, $id, EvenementDtoTransformer $evenementDtoTransformer, UserInterface $user): Response
    {

        $evenement = $EvenementRepository->find($id);
        $evenementDto = $evenementDtoTransformer->transformFromObject($evenement);
        $evenementDto->participated = $evenement->conatinsIdUser($user);
        $response = new ApiResponse("event details", $evenementDto);
        return $this->jsonResponseFactory->create($response);
    }

    #[OA\Tag('Evenement')]
    #[Route('/participer/{id}', name: 'participer', methods: 'POST')]
    public function ParticiperEvenement(
        EvenementRepository $evenementRepository,
        int                 $id,
        UserInterface       $user,
        ManagerRegistry     $doctrine
    ): Response
    {

        $evenement = $evenementRepository->find($id);
        $response = null;
        if (!$evenement) {
            throw $this->createNotFoundException('Evenement n est pas trouvé');
        }

        if ($evenement->conatinsIdUser($user)) {
            $evenement->setNbParticipants($evenement->getNbParticipants() - 1);
            $evenement->removeIdUser($user);
            $response = new ApiResponse("Vous avez annulé votre participation", null, 200);

        } else {
            $evenement->setNbParticipants($evenement->getNbParticipants() + 1);
            $evenement->addIdUser($user);
            $response = new ApiResponse("Inscription réussie à l'événement", null, 201);
        }


        $em = $doctrine->getManager();
        $em->persist($evenement);
        $em->flush();


        return $this->jsonResponseFactory->create($response);

    }

    #[OA\Tag('Evenement')]
    #[Route('/qr/{id}', name: 'qr', methods: 'GET')]
    public function qr($id, EvenementRepository $evenementRepository)
    {
        try {
            $evenement = $evenementRepository->find($id);

            if (!$evenement) {
                throw new \Exception('L\'événement correspondant n\'a pas été trouvé.');
            }

            $writer = new PngWriter();
            $qrCode = QrCode::create($evenement->getDescription())
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                ->setSize(700)
                ->setMargin(0);

            $qrCodeContent = $writer->write($qrCode)->getString();

            // Enregistrer le code QR généré dans un fichier temporaire
            $qrCodeFilePath = tempnam(sys_get_temp_dir(), 'qr_');
            file_put_contents($qrCodeFilePath, $qrCodeContent);

            // Créer une réponse avec le fichier temporaire
            $response = new BinaryFileResponse($qrCodeFilePath);

            // Ajouter les en-têtes de réponse pour forcer le téléchargement du fichier
            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'qr_code.png');
            $response->headers->set('Content-Disposition', $disposition);

            // Supprimer le fichier temporaire après avoir renvoyé la réponse
            $response->deleteFileAfterSend(true);

            // Créer la réponse JSON
            $responseData = [
                'description' => $evenement->getDescription(),
                'qr_code' => base64_encode($qrCodeContent),
            ];
            return new JsonResponse($responseData);
        } catch (\Exception $e) {
            throw $this->createNotFoundException($e->getMessage());
        }
    }


    /**
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    #[OA\Tag('Evenement')]
    #[Route('/search', name: 'evenementssearch', methods: 'GET')]
    public function afficherEvenementsSearch(Request $request, EvenementRepository $sr): Response
    {
        $requestString = $request->get('searchValue');
        $evenements = $sr->findByNom($requestString);


        $jsonContent = $this->serializer->serialize($evenements, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }]);

        $response = new ApiResponse($jsonContent, null);
        return $this->jsonResponseFactory->create($response);


    }

    #[OA\Tag('Evenement')]
    #[Route('/tri', name: 'evenementstri', methods: 'GET')]
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


        return $this->jsonResponseFactory->create($jsonContent);

    }


}