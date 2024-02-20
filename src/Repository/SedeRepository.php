<?php

namespace App\Repository;

use App\Entity\Sede;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sede>
 *
 * @method Sede|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sede|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sede[]    findAll()
 * @method Sede[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SedeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sede::class);
    }

//    /**
//     * @return Sede[] Returns an array of Sede objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sede
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
