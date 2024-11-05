<?php

namespace App\Repository;

use App\Entity\Abonnement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Abonnement>
 *
 * @method Abonnement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Abonnement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Abonnement[]    findAll()
 * @method Abonnement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbonnementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Abonnement::class);
    }

    public function save(Abonnement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Abonnement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function existAbonnementByOffre($offre): int|null
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('select count(a.id) from APP\Entity\Abonnement a where a.idOffre=:offre')
            ->setParameter('offre', $offre);
        return $query->getSingleScalarResult();
    }


    public function findByType($type): array
    {
        return $this->createQueryBuilder('a')
            ->Where('a.type LIKE :type')
            ->setParameter('type', '%' . $type . '%')
            ->getQuery()
            ->getResult();
    }

    public function findAllTypes(): array
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('select a.type from APP\Entity\Abonnement a');
        return $query->getArrayResult();
    }

    public function findAllTypesCountes($type): int|null
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('select count(a.id) from APP\Entity\Abonnement a where a.type=:type')
            ->setParameter('type', $type);
        return $query->getSingleScalarResult();
    }

    public function getPriceSum($type): int|null
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('select sum(a.prix) from APP\Entity\Abonnement a where a.type=:type')
            ->setParameter('type', $type);
        return $query->getSingleScalarResult();
    }

    public function getTotalPrice(): int|null
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('select sum(a.prix) from APP\Entity\Abonnement a');
        return $query->getSingleScalarResult();
    }

    public function getMaxPrice($type): int|null
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('select max(a.prix) from APP\Entity\Abonnement a where a.type=:type')
            ->setParameter('type', $type);
        return $query->getSingleScalarResult();
    }

    public function getMinPrice($type): int|null
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('select min(a.prix) from APP\Entity\Abonnement a where a.type=:type')
            ->setParameter('type', $type);
        return $query->getSingleScalarResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findByUserandOffre($user, $offre): Abonnement|null
    {
        return $this->createQueryBuilder('a')
            ->Where('a.idUser =:user')
            ->andWhere('a.idOffre =:offre')
            ->setParameter('user', $user)
            ->setParameter('offre', $offre)
            ->getQuery()
            ->getSingleResult();

        /*$em=$this->getEntityManager();
        $query=$em->createQuery('select a.id from APP\Entity\Abonnement a where a.idUser =:user and a.idOffre=:offre')
            ->setParameter('user', $user)
            ->setParameter('offre', $offre);
        return $query->getOneOrNullResult();*/
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findByUser($user): Abonnement|null
    {
        return $this->createQueryBuilder('a')
            ->Where('a.idUser =:user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();

        /*$em=$this->getEntityManager();
        $query=$em->createQuery('select a.id from APP\Entity\Abonnement a where a.idUser =:user and a.idOffre=:offre')
            ->setParameter('user', $user)
            ->setParameter('offre', $offre);
        return $query->getOneOrNullResult();*/
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function existUserandOffre($user, $offre): int|null
    {

        $em = $this->getEntityManager();
        $query = $em->createQuery('select count(a.id) from APP\Entity\Abonnement a where a.idUser =:user and a.idOffre=:offre')
            ->setParameter('user', $user)
            ->setParameter('offre', $offre);
        return $query->getSingleScalarResult();
    }

    public function listAbonnementsByOffre($id): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.idOffre', 'o')
            ->addSelect('o')
            ->where('a.idOffre=:id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findCurrentAbonnement($user): Abonnement|null
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('select count(o.id) from APP\Entity\Abonnement a where a.dateDebut > 0 and a.dateFin < CURRENT_DATE()');
        return $query->getSingleScalarResult();
    }
//    /**
//     * @return Abonnement[] Returns an array of Abonnement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Abonnement
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
