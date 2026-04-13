<?php

namespace App\Repository;

use App\Entity\Akce;
use App\Entity\Stitky;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Akce>
 */
class AkceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Akce::class);
    }

    public function findAkceKZobrazeniPaginated(int $limit, int $offset, ?Stitky $stitek = null, bool $probehle = false): array
    {

        $qb = $this->createQueryBuilder('a');

        if ($probehle) {
            $qb->where('
                    a.datumDo < CURRENT_DATE()
                    OR (a.datumDo IS NULL AND a.datum < CURRENT_DATE())
                ')
                ->orderBy('a.datum', 'DESC');

        } else {
            $qb->where('a.datumZobrazeniOd <= CURRENT_TIMESTAMP()')
                ->andWhere('
                    a.datumDo >= CURRENT_DATE()
                    OR (a.datumDo IS NULL AND a.datum >= CURRENT_DATE())
                ')
                ->orderBy('a.datum', 'ASC');
        }

        $qb->setMaxResults($limit)
            ->setFirstResult($offset);


        if ($stitek) {
            $qb->innerJoin('a.stitkies', 's')
                ->andWhere('s.id = :stitek')
                ->setParameter('stitek', $stitek->getId());
        }

        return $qb->getQuery()->getResult();
    }
}

