<?php

namespace App\Repository;

use App\Entity\Oeuvre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Oeuvre>
 *
 * @method Oeuvre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Oeuvre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Oeuvre[]    findAll()
 * @method Oeuvre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OeuvreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Oeuvre::class);
    }

    public function save(Oeuvre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function trierParColonneEtSens(Categorie $categorie, string $colonne, string $sens)
    {
        $qb = $this->createQueryBuilder('o');
        $qb
            ->orderBy('o.' . $colonne, $sens);

        return $qb->getQuery()->getResult();
    }

    public function remove(Oeuvre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findByCritere(string $searchTerm): array
    {
        $query = $this->createQueryBuilder('o')
            ->leftJoin('o.idCategorie', 'c')
            ->leftJoin('o.idArtiste', 'a')
            ->andWhere(' o.titre LIKE :searchTerm OR o.description LIKE :searchTerm OR o.dateCreation  LIKE :searchTerm OR c.nom  LIKE :searchTerm OR a.nom  LIKE :searchTerm OR o.lieuConservation LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->getQuery();

        return $query->getResult();
    }

    public function trierParColonne(string $colonne, string $sens)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        switch ($colonne) {
            case 'titre':
                $queryBuilder->orderBy('o.titre', $sens);
                break;
            case 'date_creation':
                $queryBuilder->orderBy('o.dateCreation', $sens);
                break;
            // Ajoute des cas pour d'autres colonnes si nécessaire
            case 'nom_artiste':
                $queryBuilder->leftJoin('o.artiste', 'a')
                    ->orderBy('a.nom', $sens);
                break;
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function trierParTitre()
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function trierParTitrePourCategorie($categorieId)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.idCategorie = :categorieId')
            ->setParameter('categorieId', $categorieId)
            ->orderBy('o.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function trierParDateCreation()
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.dateCreation', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function trierParArtiste()
    {
        return $this->createQueryBuilder('o')
            ->join('o.idArtiste', 'a')
            ->orderBy('a.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function trierParCategorie()
    {
        return $this->createQueryBuilder('o')
            ->join('o.idCategorie', 'c')
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }




}
