<?php

namespace App\Repository;

use App\Entity\HorarioTrabajo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HorarioTrabajo>
 *
 * @method HorarioTrabajo|null find($id, $lockMode = null, $lockVersion = null)
 * @method HorarioTrabajo|null findOneBy(array $criteria, array $orderBy = null)
 * @method HorarioTrabajo[]    findAll()
 * @method HorarioTrabajo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HorarioTrabajoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HorarioTrabajo::class);
    }

//    /**
//     * @return HorarioTrabajo[] Returns an array of HorarioTrabajo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HorarioTrabajo
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
