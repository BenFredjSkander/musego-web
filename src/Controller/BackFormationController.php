<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/formation', name: 'app_back_formation')]
class BackFormationController extends AbstractController
{
    #[Route('/list', name: '_list')]
    public function index(FormationRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $list = $repository->findAll();
        $pagination = $paginator->paginate(
            $list,
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('back_formation/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/add', name: '_add')]
    public function add(ManagerRegistry $doctrine, Request $request, UploaderService $fileUploader): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($formation);
            $em->flush();

            return $this->redirectToRoute('app_back_formation_list');

        }

        return $this->render('back_formation/ajouter_formation_back.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: '_delete')]
    public function delete($id, ManagerRegistry $doctrine): Response
    {
        $repositroy = $doctrine->getRepository(Formation::class);
        $formation = $repositroy->find($id);

        $em = $doctrine->getManager();
        $em->remove($formation);
        $em->flush();

        return $this->redirectToRoute('app_back_formation_list');
    }

    #[Route('/edit/{id}', name: '_update')]
    public function edit(Formation $formation, ManagerRegistry $doctrine, Request $request, UploaderService $fileUploader): Response
    {
        $em = $doctrine->getManager();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('app_back_formation_list');
        }

        return $this->render('back_formation/modifier_formation_back.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: '_show_formation', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Formation $formation): Response
    {
        return $this->render('back_formation/show.html.twig', [
            'f' => $formation,
        ]);
    }

    #[Route('/calendar', name: '_calendar')]
    public function showCalendar(): Response
    {
        return $this->render('back_formation/calendar.html.twig');
    }


}
