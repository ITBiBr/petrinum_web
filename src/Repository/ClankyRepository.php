<?php

namespace App\Repository;

use App\Entity\Clanky;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Clanky>
 */
class ClankyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Clanky::class);
    }

    //    /**
    //     * @return Clanky[] Returns an array of Clanky objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Clanky
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function search(string $term, int $limit = 10): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.titulek LIKE :term')
            ->orWhere('a.obsah LIKE :term')
            ->orWhere('a.obsahPokracovani LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
