<?php

namespace App\Repository;

use App\Entity\Consomation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Consomation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Consomation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Consomation[]    findAll()
 * @method Consomation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsomationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Consomation::class);
    }

    // /**
    //  * @return Consomation[] Returns an array of Consomation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Consomation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getPrixTotal(){
        return $this->createQueryBuilder('c')
            ->select('SUM(c.cout) as cout')
            ->getQuery()
            ->getSingleScalarResult();
        ;
    }

    public function finder( array $indice)
    {
        $query = $this->createQueryBuilder('c');
        $i = 0;
       foreach ($indice as $key => $value) {
                 $i = $i+1;
                if($key == "date1" and $value !== "vide")
                {
                    $query->andWhere('c.made_at >= :debut');
                    $query->setParameter('debut', $value); 
                }
                if($key == "date2" and $value !== "vide")
                {
                    $query->andWhere('c.made_at <= :fin');
                    $query->setParameter('fin', $value); 
                }
                if($value !== "john do" and $key !=="date1" and $key!=="date2"){
                    $query->andWhere('c.'.$key.' = :val'.$i);
                    $query->setParameter('val'.$i, $value);  
                }
              
        }
        return $query->getQuery()->getResult();
    }
    
}
