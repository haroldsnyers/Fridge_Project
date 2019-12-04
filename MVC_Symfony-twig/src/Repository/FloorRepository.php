<?php

namespace App\Repository;

use App\Entity\Floor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Floor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Floor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Floor[]    findAll()
 * @method Floor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FloorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Floor::class);
    }

    // /**
    //  * @return Floor[] Returns an array of Floor objects
    //  */

    public function findFloorsFromFridge($fridge_id)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.id_fridge = :fridge_id')
            ->setParameter('fridge_id', $fridge_id)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }



//    public function findOneById($value): ?Floor
//    {
//        try {
//            return $this->createQueryBuilder('f')
//                ->andWhere('f.id_fridge = :fridge_id')
//                ->setParameter('fridge_id', $value)
//                ->getQuery()
//                ->getOneOrNullResult();
//        } catch (NonUniqueResultException $e) {
//        }
//    }

}
