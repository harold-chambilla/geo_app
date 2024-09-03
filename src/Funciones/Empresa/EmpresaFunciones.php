<?php 
namespace App\Funciones\Empresa;

use App\Entity\Empresa;
use App\Repository\ColaboradorRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\Array_;
use Symfony\Bundle\SecurityBundle\Security;

class EmpresaFunciones
{
    public function __construct(
        private ColaboradorRepository $colaboradorRepository, 
        private Security $security,
        private EntityManagerInterface $entityManagerInterface
    ) {}

    public function obtenerEmpresa(): ?Empresa
    {
        $colaborador = $this->colaboradorRepository->findOneBy([
            'col_nombreusuario' => $this->security->getUser()->getUserIdentifier(),
        ]);

        return $colaborador ? $colaborador->getColEmpresa() : null;
    }

    public function obtenerAreas(Empresa $empresa): ?array
    {
        $areas = [];

        foreach ($empresa->getEmpAreas() as $area) {
            $puestos = [];
            foreach ($area->getAraPuestos() as $puesto) {
                $puestos[] = [
                    'id' => $puesto->getId(),
                    'nombre' => $puesto->getPstNombre(),
                ];
            }
            $areas[] = [
                'id' => $area->getId(),
                'nombre' => $area->getAraNombre(),
                'puestos' => $puestos,
            ];
        }
        return $areas ?: null;
    }

    public function obtenerSedes(Empresa $empresa): ?array
    {
        $sedes = [];
        foreach($empresa->getEmpSedes() as $sede){
            $sedes[] = [
                'id' => $sede->getId(),
                'nombre' => $sede->getSedNombre()
            ];
        }
        return $sedes ?: null;
    }
}
