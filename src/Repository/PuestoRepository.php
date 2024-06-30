<?php

namespace App\Repository;

use App\Entity\Puesto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Puesto>
 *
 * @method Puesto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Puesto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Puesto[]    findAll()
 * @method Puesto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PuestoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Puesto::class);
    }

	public function buscarPuestosPorAreaId($areaId)
	{
		return $this->createQueryBuilder('p')
			->leftJoin('p.pst_area', 'a')
			->where('a.id = :areaId')
			->setParameter('areaId', $areaId)	
		;
	}

    //    /**
    //     * @return Puesto[] Returns an array of Puesto objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Puesto
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
