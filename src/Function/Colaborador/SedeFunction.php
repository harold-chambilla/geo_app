<?php

namespace App\Function\Colaborador;

use App\Entity\Colaborador;
use App\Entity\ConfiguracionAsistencia;
use Doctrine\ORM\EntityManagerInterface;

class SedeFunction
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ){}

    public function obtenerSedePorColaborador(int $colaboradorId): array
    {
        // 1. Buscar el colaborador por ID
        $colaborador = $this->entityManager->getRepository(Colaborador::class)->find($colaboradorId);
        if (!$colaborador) {
            throw new \Exception('Colaborador no encontrado');
        }
    
        // 2. Obtener el grupo del colaborador
        $grupo = $colaborador->getGrupo();
        if (!$grupo) {
            throw new \Exception('Grupo del colaborador no encontrado');
        }
    
        // 3. Intentar obtener la configuración de asistencia con estado "puesto"
        $configuracionAsistencia = $this->entityManager->getRepository(ConfiguracionAsistencia::class)->findOneBy([
            'grupo' => $grupo,
            'cas_estado' => 'puesto'
        ]);
    
        // 4. Si no se encontró "puesto", buscar "sistema"
        if (!$configuracionAsistencia) {
            $configuracionAsistencia = $this->entityManager->getRepository(ConfiguracionAsistencia::class)->findOneBy([
                'grupo' => $grupo,
                'cas_estado' => 'sistema'
            ]);
        }
    
        // 5. Si no se encontró ninguna configuración, lanzar error
        if (!$configuracionAsistencia) {
            throw new \Exception('No se encontró una configuración de asistencia válida para el grupo del colaborador');
        }
    
        // 6. Obtener la sede asociada a la configuración de asistencia
        $sede = $configuracionAsistencia->getSede();
        if (!$sede) {
            throw new \Exception('No se encontró una sede asociada a la configuración de asistencia');
        }
    
        // 7. Retornar la sede en formato array
        return [
            'sed_id' => $sede->getId(),
            'sed_nombre' => $sede->getSedNombre(),
            'sed_pais' => $sede->getSedPais(),
            'sed_direccion' => $sede->getSedDireccion(),
            'sed_ubicacion' => $sede->getSedUbicacion(),
        ];
    }    
}