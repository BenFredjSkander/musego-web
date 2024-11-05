<?php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\SearchData;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;


/**
 * @extends ServiceEntityRepository<Avis>
 *
 * @method Avis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Avis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Avis[]    findAll()
 * @method Avis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private PaginatorInterface $paginatorInterface
    ) {
        parent::__construct($registry, Avis::class);
    }


    public function save(Avis $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Avis $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * Get published avis thanks to Search Data value
     *
     * @param SearchData $searchData
     * @return PaginationInterface
     */

    public function findBySearch(SearchData $searchData): PaginationInterface
    {
        $data = $this->createQueryBuilder('p');
           

        if (!empty($searchData->q)) {
            $data = $data
                ->andWhere('p.type LIKE :q')     
                ->setParameter('q', "{$searchData->q}");
        }
        $data = $data
            ->getQuery()
            ->getResult();



        $avis = $this->paginatorInterface->paginate($data,$searchData->page, 2);
        return $avis;


        

        
    }


//    /**
//     * @return Avis[] Returns an array of Avis objects
//     */
   public function trier(): array
   {
       return $this->createQueryBuilder('a')
           
          
           ->orderBy('a.type', 'ASC')
           
           ->getQuery()
           ->getResult()
       ;
   }

//    public function findOneBySomeField($value): ?Avis
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
