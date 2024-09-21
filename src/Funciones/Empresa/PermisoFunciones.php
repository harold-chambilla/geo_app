<?php 
namespace App\Funciones\Empresa;

use App\Entity\Colaborador;
use App\Entity\Motivo;
use App\Entity\Permiso;
use App\Repository\MotivoRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PermisoRepository;
use PDOException;

class PermisoFunciones
{
    public function __construct(
        private PermisoRepository $permisoRepository,
        private EntityManagerInterface $entityManagerInterface,
       private MotivoRepository $motivoRepository 
    )
    { }

    public function obtenerMotivos(Colaborador $colaborador): array
    {
        $motivos = [];

        // Obtener los permisos del colaborador con el estado "sistema"
        $permisos_aux = $this->permisoRepository->findBy([
            'pms_colaborador' => $colaborador,
            'pms_estado' => 'sistema',
            'pms_eliminado' => false // Asegurarse de que no estén eliminados
        ]);

        // Usar un array asociativo para evitar motivos repetidos
        $motivosUnicos = [];

        // Recorrer los permisos
        foreach ($permisos_aux as $permiso) {
            // Obtener el motivo del permiso
            $motivo = $permiso->getPmsMotivo();

            // Si el motivo no está eliminado y aún no está en la lista de motivos únicos
            if ($motivo && !$motivo->isMtvEliminado() && !isset($motivosUnicos[$motivo->getMtvNombre()])) {
                $motivosUnicos[$motivo->getMtvNombre()] = true; // Marcar como agregado
                $motivos[] = $motivo->getMtvNombre(); // Agregar el nombre del motivo a la lista
            }
        }

        // Devolver los nombres de los motivos sin repetir
        return $motivos;
    }

    public function agregarMotivos(array $nombresMotivos, Colaborador $colaborador): string
    {
        $estado = 'Ok';

        try {
            foreach ($nombresMotivos as $nombreMotivo) {
                // Verificar si el motivo ya existe
                $motivo = $this->motivoRepository->findOneBy(['mtv_nombre' => $nombreMotivo]);

                // Si el motivo no existe, lo creamos
                if (!$motivo) {
                    $motivo = new Motivo();
                    $motivo->setMtvNombre($nombreMotivo);
                    $motivo->setMtvEliminado(false);

                    // Persistir el nuevo motivo
                    $this->entityManagerInterface->persist($motivo);
                }

                // Verificar si ya existe un permiso con ese motivo para el colaborador
                $permisoExistente = $this->permisoRepository->findOneBy([
                    'pms_motivo' => $motivo,
                    'pms_colaborador' => $colaborador,
                    'pms_estado' => 'sistema',
                    'pms_eliminado' => false
                ]);

                // Si no existe el permiso, lo creamos
                if (!$permisoExistente) {
                    $permiso = new Permiso();
                    $permiso->setPmsMotivo($motivo);
                    $permiso->setPmsColaborador($colaborador);
                    $permiso->setPmsEstado('sistema'); // Estado del permiso siempre "sistema"
                    $permiso->setPmsEliminado(false);

                    // Persistir el nuevo permiso
                    $this->entityManagerInterface->persist($permiso);
                }
            }

            // Guardar todos los cambios en la base de datos
            $this->entityManagerInterface->flush();

        } catch (PDOException $e) {
            $estado = 'Error: ' . $e->getMessage(); // Capturar el error si ocurre
        }

        return $estado;
    }
}
