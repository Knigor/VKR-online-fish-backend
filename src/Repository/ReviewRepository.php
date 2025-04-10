<?php

namespace App\Repository;

use App\Entity\Products;
use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function getAverageRatingForProduct(Products $product): ?float
    {
        return $this->createQueryBuilder('r')
            ->select('AVG(r.rating) as averageRating')
            ->where('r.idProduct = :product')
            ->andWhere('r.isModerate = true')
            ->setParameter('product', $product)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
