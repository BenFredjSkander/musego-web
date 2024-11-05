<?php

namespace App\Controller\API;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use App\Repository\UserRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/avis', name: 'api_')]
class ApiAvisController extends AbstractController
{
    #[OA\Tag('Avis')]
    #[Route('/getAll', name: 'app_avis_JSON', methods: ['GET'])]
    public function index_JSON_api(SerializerInterface $serializer, AvisRepository $avisRepository, UserInterface $user): JsonResponse
    {
        $avis = $avisRepository->findBy(['idUser' => $user]);
        $json = $serializer->serialize($avis, 'json', [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['idUser'],
        ]);

        return new JsonResponse($json, 200, [], true);
    }

    #[OA\Tag('Avis')]
    #[Route('/new', name: 'create_avis', methods: ['GET'])]
    public function createAvisAction(Request $request, UserInterface $user, ValidatorInterface $validator, SerializerInterface $serializer, AvisRepository $avisRepository, UserRepository $userRepository): JsonResponse
    {
        $type = $request->get('type');
        $description = $request->get('description');
        $avisSur = $request->get('avis_sur');

        // Find the User entity associated with the given userId

        $avis = new Avis();
        $avis->setType($type);
        $avis->setDescription($description);
        $avis->setAvisSur($avisSur);
        $avis->setIdUser($user);

        $errors = $validator->validate($avis);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $avisRepository->save($avis, true);

        $jsonContent = $serializer->serialize($avis, 'json');
        return new JsonResponse($jsonContent, Response::HTTP_CREATED, [], true);
    }

    #[OA\Tag('Avis')]
    #[Route('/edit', name: 'edit_avis', methods: ['GET'])]
    public function editAvisAction(Request $request, UserInterface $user, ValidatorInterface $validator, SerializerInterface $serializer, AvisRepository $avisRepository, UserRepository $userRepository): JsonResponse
    {
        $type = $request->get('type');
        $description = $request->get('description');
        $avisSur = $request->get('avis_sur');


        $avis = $avisRepository->find($request->get('id'));
        $avis->setType($type);
        $avis->setDescription($description);
        $avis->setAvisSur($avisSur);

        $errors = $validator->validate($avis);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $avisRepository->save($avis, true);

        $jsonContent = $serializer->serialize($avis, 'json');
        return new JsonResponse($jsonContent, Response::HTTP_CREATED, [], true);
    }

    #[OA\Tag('Avis')]
    #[Route('/delete', name: 'app_avis_delete_JSON', methods: ['GET'])]
    public function delete_JSON(Request $request, AvisRepository $avisRepository): Response
    {
        $id = $request->get("id");

        $avis = $avisRepository->find($id);

        if ($avis != null) {

            $avisRepository->remove($avis, true);

            $formatted = ["message" => "Avis has been deleted successfully."];
            return new JsonResponse($formatted);
        }

        $formatted = ["error" => "Invalid avis ID."];
        return new JsonResponse($formatted, Response::HTTP_NOT_FOUND);
    }


}