<?php

namespace App\Repository;

use App\Entity\ProformatSalle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProformatSalle|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProformatSalle|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProformatSalle[]    findAll()
 * @method ProformatSalle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProformatSalleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProformatSalle::class);
    }

    // /**
    //  * @return ProformatSalle[] Returns an array of ProformatSalle objects
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
    public function findOneBySomeField($value): ?ProformatSalle
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
