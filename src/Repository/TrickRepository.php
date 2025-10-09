<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trick>
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    /**
     * @return Paginator<Trick>
     */
    public function loadMoreTricks(int $offset): Paginator
    {
        $query = $this->createQueryBuilder('t')
            ->innerJoin('t.category', 'c')
            ->addSelect('c')
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(15)
            ->setFirstResult($offset)
            ->getQuery()
        ;

        return new Paginator($query);
    }
}
