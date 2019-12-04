<?php

namespace App\Repository;

use App\Entity\Fridge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Fridge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fridge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fridge[]    findAll()
 * @method Fridge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FridgeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fridge::class);
    }

     /**
      * @return fridge[] Returns an array of fridge objects
      */

    public function findUserFridges($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.user = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneById($value): ?fridge
    {
        try {
            return $this->createQueryBuilder('f')
                ->andWhere('f.id = :fridge_id')
                ->setParameter('fridge_id', $value)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }
    }

}
