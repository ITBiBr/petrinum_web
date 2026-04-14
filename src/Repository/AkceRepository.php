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

    public function findAkceKZobrazeniPaginated(int $limit, int $offset, ?Stitky $stitek = null, bool $probehle = false, ?int $mesic = null, ?int $rok = null): array
    {

        $qb = $this->createQueryBuilder('a');

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

            $qb->orderBy('a.datum', 'DESC');


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

    private function findArchive(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT
            YEAR(COALESCE(datum_do, datum)) AS rok,
            MONTH(COALESCE(datum_do, datum)) AS mesic
        FROM akce
        WHERE datum_do < CURRENT_DATE()
           OR (datum_do IS NULL AND datum < CURRENT_DATE())
        GROUP BY rok, mesic
        ORDER BY rok DESC, mesic DESC
    ";

        return $conn->fetchAllAssociative($sql);
    }

    public function getArchiveStructured(): array
    {
        $data = $this->findArchive();

        $result = [];

        foreach ($data as $row) {
            $rok = $row['rok'];
            $mesic = (int) $row['mesic'];

            if (!isset($result[$rok])) {
                $result[$rok] = [];
            }

            $result[$rok][$mesic] = $this->getCzechMonth($mesic);
        }

        return $result;
    }

    private function getCzechMonth(int $month): string
    {
        $months = [
            1 => 'leden',
            2 => 'únor',
            3 => 'březen',
            4 => 'duben',
            5 => 'květen',
            6 => 'červen',
            7 => 'červenec',
            8 => 'srpen',
            9 => 'září',
            10 => 'říjen',
            11 => 'listopad',
            12 => 'prosinec',
        ];

        return $months[$month] ?? '';
    }


}

