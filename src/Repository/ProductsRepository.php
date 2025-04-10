<?php

namespace App\Repository;

use App\Entity\Products;
use App\Entity\Categories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    /**
     * Находит товары с учетом фильтрации и поиска
     */
    public function findWithFiltersAndSearch(string $sort = 'default', string $search = '')
    {
        $qb = $this->createQueryBuilder('p');

        // Добавляем поиск, если есть поисковый запрос
        if (!empty($search)) {
            $qb->andWhere('p.nameProduct LIKE :search OR p.descriptionProduct LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Добавляем сортировку
        switch ($sort) {
            case 'price_asc':
                $qb->orderBy('p.priceProduct', 'ASC');
                break;
            case 'price_desc':
                $qb->orderBy('p.priceProduct', 'DESC');
                break;
            default:
                // Сортировка по умолчанию (можно изменить на нужную)
                $qb->orderBy('p.id', 'ASC');
                break;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Находит товары по категории с учетом фильтрации и поиска
     */
    public function findByCategoryWithFiltersAndSearch(Categories $category, string $sort = 'default', string $search = '')
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.categories = :category')
            ->setParameter('category', $category);

        // Добавляем поиск, если есть поисковый запрос
        if (!empty($search)) {
            $qb->andWhere('p.nameProduct LIKE :search OR p.descriptionProduct LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Добавляем сортировку
        switch ($sort) {
            case 'price_asc':
                $qb->orderBy('p.priceProduct', 'ASC');
                break;
            case 'price_desc':
                $qb->orderBy('p.priceProduct', 'DESC');
                break;
            default:
                // Сортировка по умолчанию (можно изменить на нужную)
                $qb->orderBy('p.id', 'ASC');
                break;
        }

        return $qb->getQuery()->getResult();
    }
}
