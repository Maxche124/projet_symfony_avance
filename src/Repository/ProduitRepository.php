<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /* ========================= */
    /* ===== RECHERCHE ========= */
    /* ========================= */

    /**
     * Recherche par nom (LIKE)
     */
    public function searchByName(string $term): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Produits dans une plage de prix
     */
    public function findByPriceRange(string $min, string $max): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.price BETWEEN :min AND :max')
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->orderBy('p.price', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Produits les plus chers
     */
    public function findMostExpensive(int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.price', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Produits les moins chers
     */
    public function findCheapest(int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.price', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}