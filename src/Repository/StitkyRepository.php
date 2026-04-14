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

    public function findStitkySPlatnymiAkcemi(bool $probehle = false, ?int $mesic = null, ?int $rok = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.Akce', 'a');
        if ($probehle) {
            $qb->where('
                    a.datumDo < CURRENT_DATE()
                    OR (a.datumDo IS NULL AND a.datum < CURRENT_DATE())
                ');
            if ($mesic !== null && $rok !== null) {
                $od = new \DateTimeImmutable(sprintf('%d-%02d-01 00:00:00', $rok, $mesic));
                $do = $od->modify('last day of this month')->setTime(23, 59, 59);

                $qb->andWhere('
                        (
                            a.datumDo IS NULL AND a.datum BETWEEN :od AND :do
                        )
                        OR
                        (
                            a.datumDo IS NOT NULL AND a.datum <= :do AND a.datumDo >= :od
                        )
                    ')
                    ->setParameter('od', $od)
                    ->setParameter('do', $do);

            }
        } else {
            $qb->where('a.datumZobrazeniOd <= CURRENT_TIMESTAMP()')
                ->andWhere('
                    a.datumDo >= CURRENT_DATE()
                    OR (a.datumDo IS NULL AND a.datum >= CURRENT_DATE())
                ');
        }
        $qb->groupBy('s.id')
            ->orderBy('s.titulek', 'ASC');
        return $qb->getQuery()->getResult();
    }
}
