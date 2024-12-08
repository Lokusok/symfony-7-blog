<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByUser(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.user_id = :user_id')
            ->orderBy('r.createdAt', 'DESC')
            ->setParameter('user_id', $user->getId())
            ->getQuery()
            ->getResult();
    }
}
