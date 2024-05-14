<?php 
namespace App\Funciones\Empresa;

use App\Entity\Puesto;
use App\Repository\AreaRepository;
use Doctrine\ORM\EntityManagerInterface;
use PDOException;
use Symfony\Bundle\SecurityBundle\Security;

class PuestoFunciones
{
    public function __construct(
		private EntityManagerInterface $entityManagerInterface,
		private Security $security, 
		private AreaRepository $areaRepository
    ){ }

    public function registro(array $puesto): string
    {
		$area = $this->areaRepository->findOneBy([
	    	'id' => $puesto['pst_area_id'] ?? null
		]);

		$estado = 'Ok';
		$aux_puesto = new Puesto();
		$aux_puesto->setPstNombre($puesto['pst_nombre'] ?? null);
		$aux_puesto->setPstArea($area);
		$aux_puesto->setPstEliminado(0);

		try {
            $this->entityManagerInterface->persist($aux_puesto);
            $this->entityManagerInterface->flush();
        } catch (PDOException $e) {
            $estado = 'Error: '. $e; 
        }

        return $estado;
    }
}
