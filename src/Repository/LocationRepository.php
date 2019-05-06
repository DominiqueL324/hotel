<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Location|null find($id, $lockMode = null, $lockVersion = null)
 * @method Location|null findOneBy(array $criteria, array $orderBy = null)
 * @method Location[]    findAll()
 * @method Location[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Location::class);
    }

     /**
      * @return Location[] Returns an array of Location objects
      */
    
    public function findBySalle($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.salle = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }
      //  /**
     // * @return Location[] Returns an array of Location objects
     // */
    /*
    public function findBySalle($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.salle = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }*/

     public function getMaxId()
    {
        return $this->createQueryBuilder('l')
            ->select('l,MAX(l.id) AS id')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

  /*
    public function findOneBySomeField($value): ?Location
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function getPrixTotal(){
        return $this->createQueryBuilder('l')
            ->select('SUM(l.cout_total) as cout')
            ->getQuery()
            ->getSingleScalarResult();
        ;
    }

    public function finder( array $indice)
    {
        $query = $this->createQueryBuilder('l');
        $i = 0;
       foreach ($indice as $key => $value) {
                 $i = $i+1;
                if($key == "date1" and $value !== "vide")
                {
                    $query->andWhere('l.begin_at >= :debut');
                    $query->setParameter('debut', $value); 
                }
                if($key == "date2" and $value !== "vide")
                {
                    $query->andWhere('l.end_at <= :fin');
                    $query->setParameter('fin', $value); 
                }
                if($value !== "john do" and $key !=="date1" and $key!=="date2"){
                    $query->andWhere('l.'.$key.' = :val'.$i);
                    $query->setParameter('val'.$i, $value);  
                }
              
        }
        return $query->getQuery()->getResult();
    }
    
}
