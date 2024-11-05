<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Form\OffreType;
use App\Repository\AbonnementRepository;
use App\Repository\OffreRepository;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/back/offre', name: 'app_back_offre_')]
class BackOffresController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer, private LoggerInterface $loger)
    {

    }
    #[Route('/list', name: 'list')]
    public function index(OffreRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $list = $repository->findAll();
        $pagination = $paginator->paginate(
            $list,
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('back_offres/list.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/add', name: 'add')]
    public function add(ManagerRegistry $doctrine, Request $request, UploaderService $fileUploader): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imgOffre = $form->get('image')->getData();
            if ($imgOffre) {
                $imgOffreName = $fileUploader->upload($imgOffre);
                $offre->setImage($imgOffreName);
            }
            $em = $doctrine->getManager();
            $em->persist($offre);
            $em->flush();

            notyf()->addSuccess('Ajouté avec succés.');
            return $this->redirectToRoute('app_back_offre_list');

        }

        return $this->render('back_offres/add.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/edit/{id}', name: 'update')]
    public function edit($id, ManagerRegistry $doctrine, Request $request, UploaderService $fileUploader): Response
    {
        $em = $doctrine->getManager();
        $offre = $em->getRepository(Offre::class)->find($id);
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imgOffre = $form->get('image')->getData();
            if ($imgOffre) {
                $imgOffreName = $fileUploader->upload($imgOffre);
                $offre->setImage($imgOffreName);
            } else {
                $offre->setImage($offre->getImage());
            }
            $em->persist($offre);
            $em->flush();

            notyf()->addSuccess('Modifié avec succés.');
            return $this->redirectToRoute('app_back_offre_list');

        }

        return $this->render('back_offres/edit.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/detail/{id}', name: 'show')]
    public function show($id, ManagerRegistry $doctrine): Response
    {
        $repo = $doctrine->getRepository(Offre::class);
        $offre = $repo->find($id);

        return $this->render('back_offres/show.html.twig', [
            'showoffre' => $offre
        ]);

    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id, ManagerRegistry $doctrine, AbonnementRepository $repository): Response
    {
        $repositroy = $doctrine->getRepository(Offre::class);
        $offre = $repositroy->find($id);

        $count = $repository->existAbonnementByOffre($offre->getId());
        if ($count == 0) {
            $em = $doctrine->getManager();
            $em->remove($offre);
            $em->flush();

            notyf()->addSuccess('Supprimé avec succés.');

            return $this->redirectToRoute('app_back_offre_list');
        }
        notyf()->addError('Cette offre possède au moins un abonnement actif.');

        return $this->redirectToRoute('app_back_offre_list');
    }

    #[Route('/search', name: 'search')]
    public function search(Request $request, OffreRepository $repository): Response
    {
        $requestString = $request->get('query');
        $list = $repository->findByType($requestString);
        $jsonContent = $this->serializer->serialize($list, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        return new Response($jsonContent);
    }
}
