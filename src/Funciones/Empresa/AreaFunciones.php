<?php 
namespace App\Funciones\Empresa;

use App\Entity\Area;
use App\Repository\ColaboradorRepository;
use Doctrine\ORM\EntityManagerInterface;
use PDOException;
use Symfony\Bundle\SecurityBundle\Security;

class AreaFunciones
{
    public function __construct(
	private EntityManagerInterface $entityManagerInterface,
	private Security $security, 
	private ColaboradorRepository $colaboradorRepository
    ){ }

    public function registro(array $area): string
    {
	$colaborador = $this->colaboradorRepository->findOneBy([
	    'col_nombreusuario' => $this->security->getUser()->getUserIdentifier()
	]);

	$estado = 'Ok';
	$aux_area = new Area();
	$aux_area->setAraNombre($area['ara_nombre'] ?? null);
	$aux_area->setAraEmpresa($colaborador->getColEmpresa());
	$aux_area->setAraEliminado(0);

	try {
            $this->entityManagerInterface->persist($aux_area);
            $this->entityManagerInterface->flush();
        } catch (PDOException $e) {
            $estado = 'Error: '. $e; 
        }

        return $estado;
    }
}
