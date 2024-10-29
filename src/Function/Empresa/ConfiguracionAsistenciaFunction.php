<?php

namespace App\Function\Empresa;

use App\Entity\Area;
use App\Entity\ConfiguracionAsistencia;
use App\Entity\Grupo;
use App\Entity\Sede;
use App\Entity\Puesto;
use Doctrine\ORM\EntityManagerInterface;

class ConfiguracionAsistenciaFunction
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Función para registrar una nueva configuración de asistencia
    public function registrarConfiguracionAsistencia(array $configData): array
    {
        // Validar que el grupo, sede o puesto existan según los datos proporcionados.
        $grupo = $this->entityManager->getRepository(Grupo::class)->find($configData['grupo_id']);
        if (!$grupo) {
            throw new \Exception('grupo no encontrado');
        }

        $sede = $this->entityManager->getRepository(Sede::class)->find($configData['sede_id']);
        if (!$sede) {
            throw new \Exception('sede no encontrada');
        }

        $puesto = $this->entityManager->getRepository(Puesto::class)->find($configData['puesto_id']);
        if (!$puesto) {
            throw new \Exception('puesto no encontrado');
        }

        // Verificar que no exista una configuración de asistencia con el mismo grupo, sede y puesto
        $configExistente = $this->entityManager->getRepository(ConfiguracionAsistencia::class)->findOneBy([
            'grupo' => $grupo,
            'sede' => $sede,
            'puesto' => $puesto,
        ]);
        if ($configExistente) {
            throw new \Exception('configuración de asistencia ya existe para este grupo, sede y puesto');
        }

        // Crear la nueva configuración de asistencia
        $configuracionAsistencia = new ConfiguracionAsistencia();
        $configuracionAsistencia->setCasTiempoFaltaHoras($configData['cas_tiempo_falta_horas']);
        $configuracionAsistencia->setCasToleranciaIngresoMinutos($configData['cas_tolerancia_ingreso_minutos']);
        $configuracionAsistencia->setCasPermitirFoto($configData['cas_permitir_foto']);
        $configuracionAsistencia->setCasHorasextras($configData['cas_horas_extras']);
        $configuracionAsistencia->setCasFaltasTardanzas($configData['cas_faltas_tardanzas']);
        $configuracionAsistencia->setCasPermisos($configData['cas_permisos']);
        $configuracionAsistencia->setCasVacaciones($configData['cas_vacaciones']);
        $configuracionAsistencia->setCasMarcacion($configData['cas_marcacion']);
        $configuracionAsistencia->setCasModalidad($configData['cas_modalidad']);
        $configuracionAsistencia->setCasPredhorario($configData['cas_predhorario']);
        $configuracionAsistencia->setCasArea($configData['cas_area']);
        $configuracionAsistencia->setCasPuesto($configData['cas_puesto']);
        $configuracionAsistencia->setCasEstado($configData['cas_estado']);  // Sistema, Sede, Área 
        $configuracionAsistencia->setCasEliminado(false);
        $configuracionAsistencia->setGrupo($grupo);
        $configuracionAsistencia->setSede($sede);
        $configuracionAsistencia->setPuesto($puesto); 

        $this->entityManager->persist($configuracionAsistencia);
        $this->entityManager->flush();

        return [
            'cas_tiempo_falta_horas' => $configuracionAsistencia->getCasTiempoFaltaHoras(),
            'cas_tolerancia_ingreso_minutos' => $configuracionAsistencia->getCasToleranciaIngresoMinutos(),
            'cas_permitir_foto' => $configuracionAsistencia->isCasPermitirFoto(),
            'cas_horas_extras' => $configuracionAsistencia->isCasHorasextras(),
            'cas_faltas_tardanzas' => $configuracionAsistencia->isCasFaltasTardanzas(),
            'cas_permisos' => $configuracionAsistencia->isCasPermisos(),
            'cas_vacaciones' => $configuracionAsistencia->isCasVacaciones(),
            'cas_marcacion' => $configuracionAsistencia->isCasMarcacion(),
            'cas_modalidad' => $configuracionAsistencia->getCasModalidad(),
            'cas_predhorario' => $configuracionAsistencia->isCasPredhorario(),
            'cas_area' => $configuracionAsistencia->isCasArea(),
            'cas_puesto' => $configuracionAsistencia->isCasPuesto(),
            'cas_estado' => $configuracionAsistencia->getCasEstado(),
            'grupo' => $configuracionAsistencia->getGrupo()->getGrpNombre(),
            'sede' => $configuracionAsistencia->getSede()->getSedNombre(),
            'puesto' => $configuracionAsistencia->getPuesto()->getPstNombre(),
        ];
    }

    // Función para obtener una configuración de asistencia específica o la configuración con estado "sistema" por empresa
    public function obtenerConfiguracionAsistencia(int $configId = null, int $empresaId = null): array
    {
        if ($configId) {
            // Buscar la configuración de asistencia por ID
            $configuracion = $this->entityManager->getRepository(ConfiguracionAsistencia::class)->find($configId);
            if (!$configuracion) {
                throw new \Exception('Configuración de asistencia no encontrada.');
            }
        } elseif ($empresaId) {
            // Buscar la configuración de asistencia con estado "sistema" en cualquier grupo de la empresa
            $configuracion = $this->entityManager->getRepository(ConfiguracionAsistencia::class)->createQueryBuilder('ca')
                ->join('ca.grupo', 'g')
                ->where('g.empresa = :empresa')
                ->andWhere('ca.cas_estado = :estado')
                ->setParameter('empresa', $empresaId)
                ->setParameter('estado', 'sistema')
                ->getQuery()
                ->getOneOrNullResult();

            if (!$configuracion) {
                throw new \Exception('Configuración de asistencia con estado "sistema" no encontrada en la empresa.');
            }
        } else {
            throw new \Exception('Se requiere el configId o el empresaId para obtener la configuración de asistencia.');
        }

        // Retornar los datos de la configuración
        return [
            'cas_tiempo_falta_horas' => $configuracion->getCasTiempoFaltaHoras(),
            'cas_tolerancia_ingreso_minutos' => $configuracion->getCasToleranciaIngresoMinutos(),
            'cas_permitir_foto' => $configuracion->isCasPermitirFoto(),
            'cas_horas_extras' => $configuracion->isCasHorasextras(),
            'cas_faltas_tardanzas' => $configuracion->isCasFaltasTardanzas(),
            'cas_permisos' => $configuracion->isCasPermisos(),
            'cas_vacaciones' => $configuracion->isCasVacaciones(),
            'cas_marcacion' => $configuracion->isCasMarcacion(),
            'cas_modalidad' => $configuracion->getCasModalidad(),
            'cas_predhorario' => $configuracion->isCasPredhorario(),
            'cas_area' => $configuracion->isCasArea(),
            'cas_puesto' => $configuracion->isCasPuesto(),
            'cas_estado' => $configuracion->getCasEstado(),
            'grupo' => $configuracion->getGrupo()->getGrpNombre(),
            'sede' => $configuracion->getSede()->getSedNombre(),
            'puesto' => $configuracion->getPuesto()->getPstNombre(),
        ];
    }


    // Función para editar configuración de asistencia por configId, empresa con estado "sistema" o por área
    public function editarConfiguracionAsistencia(array $nuevosDatos, int $empresaId = null, int $configId = null, int $areaId = null): array
    {
        if ($configId) {
            // Buscar la configuración por ID
            $configuracion = $this->entityManager->getRepository(ConfiguracionAsistencia::class)->find($configId);
            if (!$configuracion) {
                throw new \Exception('Configuración de asistencia no encontrada.');
            }
            $configuraciones = [$configuracion];
        } elseif ($empresaId) {
            // Buscar configuración de asistencia con estado "sistema" en cualquier grupo de la empresa
            $configuracionSistema = $this->entityManager->getRepository(ConfiguracionAsistencia::class)->createQueryBuilder('ca')
                ->join('ca.grupo', 'g')
                ->where('g.empresa = :empresa')
                ->andWhere('ca.cas_estado = :estado')
                ->setParameter('empresa', $empresaId)
                ->setParameter('estado', 'sistema')
                ->getQuery()
                ->getOneOrNullResult();

            if (!$configuracionSistema) {
                throw new \Exception('Configuración de asistencia con estado "sistema" no encontrada en la empresa.');
            }
            $configuraciones = [$configuracionSistema];
        } elseif ($areaId) {
            // Buscar todas las configuraciones de asistencia de los puestos asociados al área
            $area = $this->entityManager->getRepository(Area::class)->find($areaId);
            if (!$area) {
                throw new \Exception('Área no encontrada.');
            }

            $configuraciones = [];
            foreach ($area->getPuestos() as $puesto) {
                foreach ($puesto->getConfiguracionAsistencias() as $confAsistencia) {
                    $configuraciones[] = $confAsistencia;
                }
            }
        } else {
            throw new \Exception('Se requiere el configId, empresaId o areaId para editar la configuración de asistencia.');
        }

        // Actualizar cada configuración de asistencia seleccionada con los valores proporcionados
        foreach ($configuraciones as $configuracion) {
            $configuracion->setCasTiempoFaltaHoras($nuevosDatos['cas_tiempo_falta_horas'] ?? $configuracion->getCasTiempoFaltaHoras());
            $configuracion->setCasToleranciaIngresoMinutos($nuevosDatos['cas_tolerancia_ingreso_minutos'] ?? $configuracion->getCasToleranciaIngresoMinutos());
            $configuracion->setCasPermitirFoto($nuevosDatos['cas_permitir_foto'] ?? $configuracion->isCasPermitirFoto());
            $configuracion->setCasHorasextras($nuevosDatos['cas_horas_extras'] ?? $configuracion->isCasHorasextras());
            $configuracion->setCasFaltasTardanzas($nuevosDatos['cas_faltas_tardanzas'] ?? $configuracion->isCasFaltasTardanzas());
            $configuracion->setCasPermisos($nuevosDatos['cas_permisos'] ?? $configuracion->isCasPermisos());
            $configuracion->setCasVacaciones($nuevosDatos['cas_vacaciones'] ?? $configuracion->isCasVacaciones());
            $configuracion->setCasMarcacion($nuevosDatos['cas_marcacion'] ?? $configuracion->isCasMarcacion());
            $configuracion->setCasModalidad($nuevosDatos['cas_modalidad'] ?? $configuracion->getCasModalidad());
            $configuracion->setCasPredhorario($nuevosDatos['cas_predhorario'] ?? $configuracion->isCasPredhorario());
            $configuracion->setCasArea($nuevosDatos['cas_area'] ?? $configuracion->isCasArea());
            $configuracion->setCasPuesto($nuevosDatos['cas_puesto'] ?? $configuracion->isCasPuesto());
        }

        $this->entityManager->flush();

        // Retornar los datos de la primera configuración para propósitos de respuesta
        $configuracion = $configuraciones[0];
        return [
            'cas_tiempo_falta_horas' => $configuracion->getCasTiempoFaltaHoras(),
            'cas_tolerancia_ingreso_minutos' => $configuracion->getCasToleranciaIngresoMinutos(),
            'cas_permitir_foto' => $configuracion->isCasPermitirFoto(),
            'cas_horas_extras' => $configuracion->isCasHorasextras(),
            'cas_faltas_tardanzas' => $configuracion->isCasFaltasTardanzas(),
            'cas_permisos' => $configuracion->isCasPermisos(),
            'cas_vacaciones' => $configuracion->isCasVacaciones(),
            'cas_marcacion' => $configuracion->isCasMarcacion(),
            'cas_modalidad' => $configuracion->getCasModalidad(),
            'cas_predhorario' => $configuracion->isCasPredhorario(),
            'cas_area' => $configuracion->isCasArea(),
            'cas_puesto' => $configuracion->isCasPuesto(),
            'cas_estado' => $configuracion->getCasEstado(),
            'grupo' => $configuracion->getGrupo()->getGrpNombre(),
            'sede' => $configuracion->getSede()->getSedNombre(),
            'puesto' => $configuracion->getPuesto()->getPstNombre(),
        ];
    }

    // Función para eliminar una configuración de asistencia (cambio de estado lógico)
    public function eliminarConfiguracionAsistencia(int $configId): void
    {
        $configuracion = $this->entityManager->getRepository(ConfiguracionAsistencia::class)->find($configId);
        if (!$configuracion) {
            throw new \Exception('Configuración de asistencia no encontrada.');
        }

        $configuracion->setCasEliminado(true);
        $this->entityManager->flush();
    }
}
