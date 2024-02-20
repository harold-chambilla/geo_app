<?php

namespace App\Repository;

use App\Entity\RegistroCambios;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RegistroCambios>
 *
 * @method RegistroCambios|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegistroCambios|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegistroCambios[]    findAll()
 * @method RegistroCambios[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistroCambiosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegistroCambios::class);
    }

//    /**
//     * @return RegistroCambios[] Returns an array of RegistroCambios objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RegistroCambios
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
