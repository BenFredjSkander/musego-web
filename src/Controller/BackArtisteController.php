<?php

namespace App\Controller;

use App\Entity\Artiste;
use App\Form\ArtisteFormType;
use App\Repository\ArtisteRepository;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/back/artiste', name: 'app_back_artistes_')]
class BackArtisteController extends AbstractController
{
    public function __construct(private LoggerInterface $logger, private SerializerInterface $serializer)
    {

    }

    //Affichage de tous: + Repository
    #[Route('', name: 'list')]
    public function list(ArtisteRepository $repo): Response
    {
        $artistes = $repo->findAll();
        return $this->render('back_artiste/index.html.twig', [
            'artistes' => $artistes,
        ]);
    }

    //Ajout
    #[Route('/ajouter', name: 'add')]
    public function addArtiste(ManagerRegistry $doctrine, Request $request, UploaderService $uploaderService): Response
    {
        $artiste = new Artiste();
        $form = $this->createForm(ArtisteFormType::class, $artiste);
        // $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $posterFile = $form->get('image')->getData();
            if ($posterFile) {
                $posterFileName = $uploaderService->upload($posterFile);
                $artiste->setImage($posterFileName);
            }

            $em = $doctrine->getManager();
            $em->persist($artiste);
            $em->flush();
            return $this->redirectToRoute('app_back_artistes_list');
        }
        return $this->render('back_artiste/addArtiste.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    //Affichage d'1 seul:
    #[Route('/showArtiste/{id}', name: 'showArtiste')]
    public function showArtiste($id, ManagerRegistry $doctrine): Response
    {
        //Trouver le bon Artiste
        $repoC = $doctrine->getRepository(Artiste::class);
        $artiste = $repoC->find($id);


        return $this->render('artiste/showC.html.twig', [
            'artiste' => $artiste,
        ]);
    }

    //Modification:
    #[Route('/edit/{id}', name: 'update')]
    public function update(Request $request, ManagerRegistry $doctrine, Artiste $artiste, UploaderService $uploaderService): Response
    {
        $form = $this->createForm(ArtisteFormType::class, $artiste);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $posterFile = $form->get('image')->getData();
            if ($posterFile) {
                $posterFileName = $uploaderService->upload($posterFile);
                $artiste->setImage($posterFileName);
            }
            $entityManager = $doctrine->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('app_back_artistes_list');
        }
        return $this->render('back_artiste/updateArtiste.html.twig', [
            'form' => $form->createView(),
        ]);
    }

//Suppression:
    #[Route('/{id}/delete', name: 'delete')]
    public function deleteArtiste($id, ManagerRegistry $doctrine): Response
    {
        //Trouver le bon Artiste
        $repoC = $doctrine->getRepository(Artiste::class);
        $artiste = $repoC->find($id);
        //Utiliser Manager pour supprimer le artiste trouvé
        $em = $doctrine->getManager();
        $em->remove($artiste);
        $em->flush();

        return $this->redirectToRoute('app_back_artistes_list');
    }


    #[Route('/export', name: 'exporterExcel')]
    public function exporterExcel(ArtisteRepository $artisteRepository): Response
    {
        // Récupérer tous les artistes depuis la base de données
        $artistes = $artisteRepository->findAll();

        // Créer un nouveau classeur Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Ajouter les en-têtes dans la première ligne
        $sheet->setCellValue('A1', 'Nom');
        $sheet->setCellValue('B1', 'Prénom');
        $sheet->setCellValue('C1', 'Date de naissance');
        $sheet->setCellValue('D1', 'CIN');
        $sheet->setCellValue('E1', 'Email');
        $sheet->setCellValue('F1', 'Adresse');
        $sheet->setCellValue('G1', 'Spécialité');
        $sheet->setCellValue('H1', 'Nationalité');

        // Appliquer un style aux en-têtes de la première ligne
        $firstRowStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => [
                    'rgb' => 'F2F2F2',
                ],
            ],
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($firstRowStyle);

        // Itérer sur chaque artiste et ajouter les données dans les cellules de la feuille de calcul
        $row = 2;
        foreach ($artistes as $artiste) {
            $nom = $artiste->getNom();
            $prenom = $artiste->getPrenom();
            $dateNaissance = $artiste->getDateNaissance()->format('d/m/Y');
            $cin = $artiste->getCin();
            $email = $artiste->getEmail();
            $adresse = $artiste->getAdresse();
            $specialite = $artiste->getSpecialite();
            $nationalite = $artiste->getNationalite();

            $sheet->setCellValue('A' . $row, $nom);
            $sheet->setCellValue('B' . $row, $prenom);
            $sheet->setCellValue('C' . $row, $dateNaissance);
            $sheet->setCellValue('D' . $row, $cin);
            $sheet->setCellValue('E' . $row, $email);
            $sheet->setCellValue('F' . $row, $adresse);
            $sheet->setCellValue('G' . $row, $specialite);
            $sheet->setCellValue('H' . $row, $nationalite);

            $row++;
        }

        // Ajouter des bordures à toutes les cellules
        $allBordersStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => [
                        'rgb' => 'C2C2C2',
                    ],
                ],
            ],
        ];
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray($allBordersStyle);


// Ajuster la largeur des colonnes en fonction de leur contenu
        foreach (range('A', $highestColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

// Créer une réponse HTTP avec le contenu du fichier Excel exporté
        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="export_artistes.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        $writer = new Xlsx($spreadsheet);
        ob_start(); // démarrer la temporisation de sortie
        $writer->save('php://output');
        $content = ob_get_clean(); // récupérer la sortie générée dans une variable et arrêter la temporisation

        $response->setContent($content);

        return $response;

    }

    /**
     * @throws ExceptionInterface
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    #[Route('/search', name: 'artistesSearch')]
    public function searchAction(Request $request, ArtisteRepository $artisteRepository): Response
    {
        $searchTerm = $request->query->get('query');

        $artistes = $artisteRepository->findByCritere($searchTerm);

        $jsonContent = $this->serializer->serialize($artistes, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new Response($jsonContent);
    }


}