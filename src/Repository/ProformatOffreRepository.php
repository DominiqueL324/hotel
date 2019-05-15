<?php

namespace App\Repository;

use App\Entity\ProformatOffre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProformatOffre|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProformatOffre|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProformatOffre[]    findAll()
 * @method ProformatOffre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProformatOffreRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProformatOffre::class);
    }

    // /**
    //  * @return ProformatOffre[] Returns an array of ProformatOffre objects
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
    public function findOneBySomeField($value): ?ProformatOffre
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
