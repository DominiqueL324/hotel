<?php

namespace App\Repository;

use App\Entity\Proformat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Proformat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proformat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proformat[]    findAll()
 * @method Proformat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProformatRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Proformat::class);
    }

    // /**
    //  * @return Proformat[] Returns an array of Proformat objects
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
    public function findOneBySomeField($value): ?Proformat
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
