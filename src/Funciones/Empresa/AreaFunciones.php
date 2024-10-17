<?php 
namespace App\Funciones\Empresa;

use App\Entity\Area;
use App\Entity\Empresa;
use App\Entity\Puesto;
use App\Repository\AreaRepository;
use App\Repository\ColaboradorRepository;
use App\Repository\PuestoRepository;
use Doctrine\ORM\EntityManagerInterface;
use PDOException;
use Symfony\Bundle\SecurityBundle\Security;

class AreaFunciones
{
    public function __construct(
		private EntityManagerInterface $entityManagerInterface,
		private Security $security, 
        private ColaboradorRepository $colaboradorRepository,
        private AreaRepository $areaRepository,
        private PuestoRepository $puestoRepository
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

    public function obtenerAreas(Empresa $empresa): array
    {
        // Obtener todas las áreas de la empresa
        $areas = $empresa->getEmpAreas();

        // Estructura de datos para almacenar el resultado
        $resultado = [];

        // Iterar sobre las áreas para extraer su información
        foreach ($areas as $area) {
            // Obtener los puestos asociados con esta área
            $puestos = [];
            foreach ($area->getAraPuestos() as $puesto) {
                // Añadir la información del puesto
                $puestos[] = [
                    'id' => $puesto->getId(),
                    'nombre' => $puesto->getPstNombre(),
                    'eliminado' => $puesto->isPstEliminado()
                ];
            }

            // Añadir la información del área y sus puestos
            $resultado[] = [
                'id' => $area->getId(),
                'nombre' => $area->getAraNombre(),
                'eliminado' => $area->isAraEliminado(),
                'puestos' => $puestos
            ];
        }

        return $resultado;
    }

    public function registroAreasYPuestos(array $areas, Empresa $empresa): string
    {
        $estado = 'Ok';

        // Iterar sobre cada área del array $areas
        foreach ($areas as $areaData) {
            // Verificar si el área tiene un 'id', lo que significa que ya existe en la base de datos
            if (isset($areaData['id'])) {
                // Buscar el área existente en la base de datos
                $aux_area = $this->areaRepository->find($areaData['id']);

                // Si el área no existe, devolver un error (esto es opcional)
                if (!$aux_area) {
                    return 'Error: Área con ID ' . $areaData['id'] . ' no encontrada';
                }

                // Actualizar los datos del área si es necesario
                $aux_area->setAraNombre($areaData['nombre'] ?? $aux_area->getAraNombre());

            } else {
                // Si no hay 'id', significa que es un área nueva, entonces la creamos
                $aux_area = new Area();
                $aux_area->setAraNombre($areaData['nombre'] ?? null);
                $aux_area->setAraEmpresa($empresa);
                $aux_area->setAraEliminado(0);
            }

            try {
                // Guardar o actualizar el área
                $this->entityManagerInterface->persist($aux_area);

                // Ahora, iterar sobre los puestos del área
                if (!empty($areaData['puestos'])) {
                    foreach ($areaData['puestos'] as $puestoData) {
                        // Verificar si el puesto tiene un 'id', lo que significa que ya existe
                        if (isset($puestoData['id'])) {
                            // Buscar el puesto existente en la base de datos
                            $aux_puesto = $this->puestoRepository->find($puestoData['id']);

                            // Si el puesto no existe, devolver un error (esto es opcional)
                            if (!$aux_puesto) {
                                return 'Error: Puesto con ID ' . $puestoData['id'] . ' no encontrado';
                            }

                            // Actualizar los datos del puesto si es necesario
                            $aux_puesto->setPstNombre($puestoData['nombre'] ?? $aux_puesto->getPstNombre());

                        } else {
                            // Si no hay 'id', significa que es un puesto nuevo
                            $aux_puesto = new Puesto();
                            $aux_puesto->setPstNombre($puestoData['nombre'] ?? null);
                            $aux_puesto->setPstArea($aux_area); // Asociar el puesto con el área
                            $aux_puesto->setPstEliminado(0);
                        }

                        // Guardar o actualizar el puesto
                        $this->entityManagerInterface->persist($aux_puesto);
                    }
                }

                // Aplicar los cambios a la base de datos
                $this->entityManagerInterface->flush();

            } catch (PDOException $e) {
                $estado = 'Error: '. $e->getMessage(); // Manejar los errores
                return $estado; // Si hay un error, devolver el estado
            }
        }

        return $estado; // Devolver el estado 'Ok' si todo fue exitoso
    }

    public function obtenerEmpleadosPorAreaYPuesto(Empresa $empresa): array
    {
        // Obtener todas las áreas de la empresa
        $areas = $empresa->getEmpAreas();
        $resultado = [];

        foreach ($areas as $area) {
            $puestos = [];

            foreach ($area->getAraPuestos() as $puesto) {
                // Obtener los colaboradores asociados a este puesto
                $colaboradores = [];
                foreach ($puesto->getPstColaboradores() as $colaborador) {
                    if (!$colaborador->isColEliminado()) {
                        $colaboradores[] = [
                            'id' => $colaborador->getId(),
                            'nombre' => $colaborador->getColNombres(),
                            'apellidos' => $colaborador->getColApellidos(),
                            'dni' => $colaborador->getColDninit(),
                            'correo' => $colaborador->getColCorreoelectronico(),
                            'usuario' => $colaborador->getColNombreusuario(),
                        ];
                    }
                }

                // Añadir la información del puesto y sus colaboradores
                if (!$puesto->isPstEliminado()) {
                    $puestos[] = [
                        'id' => $puesto->getId(),
                        'nombre' => $puesto->getPstNombre(),
                        'eliminado' => $puesto->isPstEliminado(),
                        'colaboradores' => $colaboradores,
                    ];
                }
            }

            // Añadir la información del área y sus puestos
            if (!$area->isAraEliminado()) {
                $resultado[] = [
                    'id' => $area->getId(),
                    'nombre' => $area->getAraNombre(),
                    'eliminado' => $area->isAraEliminado(),
                    'puestos' => $puestos,
                ];
            }
        }

        return $resultado;
    }
}
