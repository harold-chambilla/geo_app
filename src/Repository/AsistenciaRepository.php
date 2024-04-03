<?php

namespace App\Repository;

use App\Entity\Asistencia;
use App\Entity\Colaborador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Asistencia>
 *
 * @method Asistencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method Asistencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method Asistencia[]    findAll()
 * @method Asistencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AsistenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Asistencia::class);
    }

//    /**
//     * @return Asistencia[] Returns an array of Asistencia objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Asistencia
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findLastAsistenciaByUser($user): ?Asistencia
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.asi_colaborador = :user')
            ->setParameter('user', $user)
            ->andWhere('a.asi_estadosalida IS NULL')
            ->orderBy('a.id', 'DESC') // Ordenar por ID en orden descendente para obtener el último registro
            ->setMaxResults(1) // Obtener solo un resultado (el último registro)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLastAsistenciaByUserEntrada($user): ?Asistencia
    {
        return $this->createQueryBuilder('a')
        ->andWhere('a.asi_colaborador = :user')
        ->setParameter('user', $user)
        ->andWhere('a.asi_estadoentrada IS NOT NULL') // Filtrar por datos de entrada no nulos
        ->andWhere('a.asi_estadosalida IS NULL') // Filtrar por datos de salida nulos
        ->orderBy('a.id', 'DESC') // Ordenar por ID en orden descendente para obtener el último registro
        ->setMaxResults(1) // Obtener solo un resultado (el último registro)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function findLastAsistenciaByUserSalida($user): ?Asistencia
    {
        return $this->createQueryBuilder('a')
        ->andWhere('a.asi_colaborador = :user')
        ->setParameter('user', $user)
        ->andWhere('a.asi_estadoentrada IS NOT NULL') // Filtrar por datos de entrada nulos
        ->andWhere('a.asi_estadosalida IS NOT NULL') // Filtrar por datos de salida no nulos
        ->orderBy('a.id', 'DESC') // Ordenar por ID en orden descendente para obtener el último registro
        ->setMaxResults(1) // Obtener solo un resultado (el último registro)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function findAsistenciaByFecha(Colaborador $colaborador, string $fecha): ?Asistencia
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.asi_colaborador = :colaborador')
            ->andWhere('a.asi_fechaentrada = :fecha')
            ->setParameter('colaborador', $colaborador)
            ->setParameter('fecha', $fecha)
            ->orderBy('a.id', 'ASC') // Ordenar por el identificador de la asistencia
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
