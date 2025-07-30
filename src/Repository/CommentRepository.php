<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @param Trick $trick
     * @param int $offset
     * @return Paginator<Comment>
     */
    public function loadMoreComments(Trick $trick, int $offset): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->innerJoin('c.trick', 't')
            ->andWhere('c.trick = :trick')
            ->setParameter('trick', $trick)
            ->orderBy('c.id', 'DESC')
            ->setMaxResults(4)
            ->setFirstResult($offset)
            ->getQuery()
        ;

        return new Paginator($query);
    }
}
