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

    public function getTrickWithCommentsAndImagesAndVideos(int $trickId): ?Trick
    {
        return $this->createQueryBuilder('trick')
            ->addSelect('trick')
            ->leftJoin('trick.mainImage', 'mainImage')
            ->addSelect('mainImage')
            ->leftJoin('trick.comments', 'comment')
            ->addSelect('comment')
            ->leftJoin('trick.images', 'image')
            ->addSelect('image')
            ->leftJoin('trick.videos', 'video')
            ->addSelect('video')
            ->leftJoin('trick.user', 'user')
            ->addSelect('user')
            ->leftJoin('trick.category', 'category')
            ->addSelect('category')
            ->leftJoin('comment.user', 'comment_user')
            ->addSelect('comment_user')
            ->andWhere('trick.id = :id')
            ->setParameter('id', $trickId)
            ->orderBy('trick.id', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
