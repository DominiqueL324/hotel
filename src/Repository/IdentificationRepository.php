<?php

namespace App\Repository;

use App\Entity\Identification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Identification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Identification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Identification[]    findAll()
 * @method Identification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdentificationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Identification::class);
    }

    // /**
    //  * @return Identification[] Returns an array of Identification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function getMaxId()
    {
        return $this->createQueryBuilder('i')
            ->select('i,MAX(i.id) AS id')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    /*
    public function findOneBySomeField($value): ?Identification
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

     public function getPrixTotal(){
        return $this->createQueryBuilder('i')
            ->select('SUM(i.cout) as cout')
            ->getQuery()
            ->getSingleScalarResult();
        ;
    }

    public function finder( array $indice)
    {
        $query = $this->createQueryBuilder('i');
        $i = 0;
       foreach ($indice as $key => $value) {
                 $i = $i+1;
                if($key == "date1" and $value !== "vide")
                {
                    $query->andWhere('i.arrived_at >= :debut');
                    $query->setParameter('debut', $value); 
                }
                if($key == "date2" and $value !== "vide")
                {
                    $query->andWhere('i.lived_at <= :fin');
                    $query->setParameter('fin', $value); 
                }
                if($value !== "john do" and $key !=="date1" and $key!=="date2"){
                    $query->andWhere('i.'.$key.' = :val'.$i);
                    $query->setParameter('val'.$i, $value);  
                }
              
        }
        return $query->getQuery()->getResult();
    }
}
