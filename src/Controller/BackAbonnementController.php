<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Form\AbonnementType;
use App\Repository\AbonnementRepository;
use App\Repository\OffreRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Histogram;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/back/abonnement', name: 'app_back_abonnement_')]
class BackAbonnementController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer)
    {

    }

    #[Route('/list', name: 'list')]
    public function index(AbonnementRepository $repository,
                          PaginatorInterface   $paginator,
                          Request              $request): Response
    {
        $list = $repository->findAll();
        $pagination = $paginator->paginate(
            $list,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('back_abonnement/list.html.twig', [
            'list_abonnement' => $list, 'pagination' => $pagination
        ]);
    }


    #[Route('/chart', name: 'chart')]
    public function indexAction(AbonnementRepository $repository)
    {
        $total = $repository->getTotalPrice();
        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable(
            [['Abonnements', 'Nombre Abonnés'],
                ['Hebdomadaire', $repository->findAllTypesCountes("Hebdomadaire")],
                ['Mensuel', $repository->findAllTypesCountes("Mensuel")],
                ['Annuel', $repository->findAllTypesCountes("Annuel")],
            ]
        );
        $pieChart->getOptions()->setTitle('Pourcentage des abonnements effectués');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#000000');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);

        $bar = new BarChart();
        $bar->getData()->setArrayToDataTable([
            ['Prix', 'min', 'max'],

            ['Hebdomadaire', $repository->getMinPrice("Hebdomadaire"), $repository->getMaxPrice("Hebdomadaire")],

            ['Mensuel', $repository->getMinPrice("Mensuel"), $repository->getMaxPrice("Mensuel")],
            ['Annuel', $repository->getMinPrice("Annuel"), $repository->getMaxPrice("Annuel")]
        ]);
        $bar->getOptions()->setTitle('Evolution des écarts des prix d\'abonnements, en €');
        $bar->getOptions()->getHAxis()->setTitle('Prix');
        $bar->getOptions()->getHAxis()->setMinValue(0);
        $bar->getOptions()->getVAxis()->setTitle('Abonnements');
        $bar->getOptions()->setWidth(900);
        $bar->getOptions()->setHeight(500);

        $histo = new Histogram();
        $histo->getData()->setArrayToDataTable([
            ['Abonnement', 'Somme totale'],
            ['Hebdomadaire', $repository->getPriceSum("Hebdomadaire")],
            ['Mensuel', $repository->getPriceSum("Mensuel")],
            ['Annuel', $repository->getPriceSum("Annuel")]]);
        $histo->getOptions()->setTitle('Somme des prix d\'abonnements, en €');
        $histo->getOptions()->getHAxis()->setTitle('Total des prix :' . $total . '€');
        $histo->getOptions()->setWidth(900);
        $histo->getOptions()->setHeight(500);
        $histo->getOptions()->getLegend()->setPosition('none');
        $histo->getOptions()->setColors(['green']);

        return $this->render('back_abonnement/show.html.twig', array('piechart' => $pieChart, 'bar' => $bar, 'histo' => $histo, 'total' => $total));
    }

    #[Route('/list/offre/{id}', name: 'list_offre')]
    public function indexOffre(OffreRepository      $repositoryo,
                               AbonnementRepository $repositorya,
                                                    $id, PaginatorInterface $paginator, Request $request): Response
    {
        $list = $repositorya->listAbonnementsByOffre($id);
        $pagination = $paginator->paginate(
            $list,
            $request->query->getInt('page', 1),
            10
        );
//        $message = 'Liste des abonnements pour offre : ' . $offre->getType();
        return $this->render('back_abonnement/list.html.twig', [
            'list_abonnement' => $list, 'pagination' => $pagination
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(ManagerRegistry $doctrine, Request $request): Response
    {
        $abonnement = new Abonnement();
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($abonnement);
            $em->flush();
            notyf()->addSuccess('Ajouté avec succés.');

            return $this->redirectToRoute('app_back_abonnement_list');

        }

        return $this->render('back_abonnement/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'update')]
    public function edit($id, ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $abonnement = $em->getRepository(Abonnement::class)->find($id);
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            notyf()->addSuccess('Modifié avec succés.');
            return $this->redirectToRoute('app_back_abonnement_list');
        }
        return $this->render('back_abonnement/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/detail/{id}', name: 'show')]
    public function show($id, ManagerRegistry $doctrine): Response
    {
        $repo = $doctrine->getRepository(Abonnement::class);
        $abonnement = $repo->find($id);

        return $this->render('back_abonnement/show.html.twig', [
            'showAbonnement' => $abonnement
        ]);

    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id, ManagerRegistry $doctrine): Response
    {
        $repositroy = $doctrine->getRepository(Abonnement::class);
        $abonnement = $repositroy->find($id);

        $em = $doctrine->getManager();
        $em->remove($abonnement);
        $em->flush();

        notyf()->addSuccess('Supprimé avec succés.');

        return $this->redirectToRoute('app_back_abonnement_list');

    }

    #[Route('/search', name: 'search')]
    public function search(Request $request, AbonnementRepository $repository): Response
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
