<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Menu>
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function findTree(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.parent IS NULL')
            ->leftJoin('m.children', 'c')
            ->addOrderBy('m.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
