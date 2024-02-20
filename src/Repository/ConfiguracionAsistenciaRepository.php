<?php

namespace App\Repository;

use App\Entity\ConfiguracionAsistencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConfiguracionAsistencia>
 *
 * @method ConfiguracionAsistencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfiguracionAsistencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfiguracionAsistencia[]    findAll()
 * @method ConfiguracionAsistencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfiguracionAsistenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfiguracionAsistencia::class);
    }

//    /**
//     * @return ConfiguracionAsistencia[] Returns an array of ConfiguracionAsistencia objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ConfiguracionAsistencia
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
