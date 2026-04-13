<?php

namespace App\Repository;

use App\Entity\Stitky;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stitky>
 */
class StitkyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stitky::class);
    }

    public function findStitkySAkcemi(): array
    {
        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.Akce', 'a');
        $qb->groupBy('s.id')
            ->orderBy('s.titulek', 'ASC');
        return $qb->getQuery()->getResult();
    }
}
