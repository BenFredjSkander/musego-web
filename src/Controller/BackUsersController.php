<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/back/users', name: 'app_back_users_')]
class BackUsersController extends AbstractController
{

    public function __construct(private ManagerRegistry $doctrine, private SerializerInterface $serializer)
    {
    }

    #[Route('', name: 'list')]
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $users = $userRepository->findAll();
        $pagination = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('back_users/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/{id}', name: 'profile', requirements: ['id' => '\d+'])]
    public function showProfile(User $user, Request $request, UserRepository $repository): Response
    {
        $u = $repository->find($user->getId());
        return $this->render('back_users/profile.html.twig', [
            'user' => $u,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(User $user, Request $request): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->getManager()->flush();
            return $this->redirectToRoute('app_back_users_list');
        }
        return $this->renderForm("back_users/edit.html.twig",
            ['form' => $form]);
    }

    #[Route('/ban/{id}', name: 'ban')]
    public function ban(ManagerRegistry $mr, User $user): Response
    {
        $user->setLocked(!$user->isLocked());
        $em = $mr->getManager();
        $em->flush();
        return $this->redirectToRoute('app_back_users_list');
    }


    /**
     * @throws ExceptionInterface
     */
    #[Route('/find', name: 'ajax_search')]
    public function searchAction(Request $request, UserRepository $userRepository): Response
    {
        $requestString = $request->get('query');

        $users = $userRepository->searchByEmailOrName($requestString);

        $jsonContent = $this->serializer->serialize($users, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new Response($jsonContent);
    }
}
