<?php

namespace App\Repository;

use App\Entity\ProformatRepas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProformatRepas|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProformatRepas|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProformatRepas[]    findAll()
 * @method ProformatRepas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProformatRepasRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProformatRepas::class);
    }

    // /**
    //  * @return ProformatRepas[] Returns an array of ProformatRepas objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProformatRepas
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
