<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Form\AtelierType;
use App\Repository\AtelierRepository;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/back/atelier', name: 'app_back_atelier')]
class BackAtelierController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer, private LoggerInterface $loger)
    {

    }

    #[Route('/list', name: '_list')]
    public function index(AtelierRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $list = $repository->findAll();
        $pagination = $paginator->paginate(
            $list,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('back_atelier/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/add', name: '_add')]
    public function add(ManagerRegistry $doctrine, Request $request, UploaderService $fileUploader): Response
    {
        $atelier = new Atelier();
        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $posterFile = $form->get('image')->getData();
            if ($posterFile) {
                $posterFileName = $fileUploader->upload($posterFile);
                $atelier->setImage($posterFileName);
            } //permet d'upload l'image dans ma table
            $em = $doctrine->getManager();
            $em->persist($atelier);
            $em->flush();

            return $this->redirectToRoute('app_back_atelier_list');

        }

        return $this->render('back_atelier/ajouter_atelier_back.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: '_delete')]
    public function delete($id, ManagerRegistry $doctrine): Response
    {
        $repositroy = $doctrine->getRepository(Atelier::class);
        $atelier = $repositroy->find($id);

        $em = $doctrine->getManager();
        $em->remove($atelier);
        $em->flush();

        return $this->redirectToRoute('app_back_atelier_list');
    }

    #[Route('/edit/{id}', name: '_update')]
    public function edit(Atelier $atelier, ManagerRegistry $doctrine, Request $request, UploaderService $fileUploader): Response
    {
        $em = $doctrine->getManager();
        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $posterFile = $form->get('image')->getData();
            if ($posterFile) {
                $posterFileName = $fileUploader->upload($posterFile);
                $atelier->setImage($posterFileName);
            } //upload l'image
            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('app_back_atelier_list');
        }

        return $this->render('back_atelier/modifier_atelier_back.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/searchAteliers', name: '_SearchAteliers')]
    public function searchAteliers(Request $request, AtelierRepository $repository): Response
    {
        $requestString = $request->get('query');
        $list = $repository->findByName($requestString); //assure la recherche de n'importe quelle valeurdu champs nom
        $jsonContent = $this->serializer->serialize($list, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]); //permet la tranformation d'un objet entier en informations json
        return new Response($jsonContent);
    }


}
