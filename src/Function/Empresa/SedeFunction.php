<?php

namespace App\Function\Empresa;

use App\Entity\Sede;
use App\Entity\Empresa;
use App\Entity\ConfiguracionAsistencia;
use App\Entity\Grupo;
use App\Entity\Puesto;
use Doctrine\ORM\EntityManagerInterface;

class SedeFunction
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Función para registrar una nueva sede y su configuración de asistencia
    public function registrarSede(int $empresaId, array $sedeData): array
    {
        // Buscar la empresa por su ID
        $empresa = $this->entityManager->getRepository(Empresa::class)->find($empresaId);
        if (!$empresa) {
            throw new \Exception('empresa no encontrada');
        }

        // Buscar el grupo de nombre "General" relacionado con la empresa
        $grupo = $this->entityManager->getRepository(Grupo::class)->findOneBy([
            'empresa' => $empresa,
            'grp_nombre' => 'general',
        ]);
        if (!$grupo) {
            throw new \Exception('grupo "general" no encontrado en esta empresa');
        }

        // Buscar una sede existente vinculada a un grupo por nombre o ubicación
        $sedeExistente = $this->entityManager->getRepository(Sede::class)->createQueryBuilder('s')
            ->join('s.configuracionesAsistencias', 'ca')
            ->where('ca.grupo = :grupo')
            ->andWhere('s.sedNombre = :nombre OR s.sedUbicacion = :ubicacion')
            ->setParameter('grupo', $grupo)
            ->setParameter('nombre', $sedeData['sed_nombre'])
            ->setParameter('ubicacion', $sedeData['sed_ubicacion'])
            ->getQuery()
            ->getOneOrNullResult();

        if ($sedeExistente) {
            throw new \Exception('ya existe una sede con el mismo nombre o ubicación en esta empresa');
        }

        // Buscar la configuración de asistencia con estado "Sistema" vinculada al grupo
        $configuracionSistema = $this->entityManager->getRepository(ConfiguracionAsistencia::class)->findOneBy([
            'grupo' => $grupo,
            'cas_estado' => 'sistema',
        ]);
        if (!$configuracionSistema) {
            throw new \Exception('configuración de asistencia con estado "sistema" no encontrada en este grupo');
        }

        // Obtener el puesto "Superadministrador" vinculado a la configuración de asistencia del grupo general
        $puestoSuperadmin = $configuracionSistema->getPuesto();
        if (!$puestoSuperadmin || $puestoSuperadmin->getPstNombre() !== 'superadministrador') {
            throw new \Exception('puesto "superadministrador" no encontrado en la configuración de asistencia del sistema');
        }

        // Crear la nueva sede
        $sede = new Sede();
        $sede->setSedNombre($sedeData['sed_nombre']);
        $sede->setSedPais($sedeData['sed_pais']);
        $sede->setSedDireccion($sedeData['sed_direccion']);
        $sede->setSedUbicacion($sedeData['sed_ubicacion']);
        $sede->setSedEliminado(false);

        $this->entityManager->persist($sede);

        // Crear la configuración de asistencia relacionada
        $configuracionAsistencia = new ConfiguracionAsistencia();
        $configuracionAsistencia->setCasTiempoFaltaHoras(0);
        $configuracionAsistencia->setCasToleranciaIngresoMinutos(0);
        $configuracionAsistencia->setCasPermitirFoto(false);
        $configuracionAsistencia->setCasHorasextras(false);
        $configuracionAsistencia->setCasFaltasTardanzas(false);
        $configuracionAsistencia->setCasPermisos(false);
        $configuracionAsistencia->setCasVacaciones(false);
        $configuracionAsistencia->setCasMarcacion(false);
        $configuracionAsistencia->setCasModalidad('["MOD_PRESENCIAL"]');
        $configuracionAsistencia->setCasArea(false);
        $configuracionAsistencia->setCasPuesto(false);
        $configuracionAsistencia->setCasPredhorario(false);
        $configuracionAsistencia->setCasEliminado(false);
        $configuracionAsistencia->setCasEstado('sede');
        $configuracionAsistencia->setGrupo($grupo);
        $configuracionAsistencia->setSede($sede);
        $configuracionAsistencia->setPuesto($puestoSuperadmin);

        $this->entityManager->persist($configuracionAsistencia);

        // Guardar los cambios en la base de datos
        $this->entityManager->flush();

        return [
            'sed_id' => $sede->getId(),
            'sed_nombre' => $sede->getSedNombre(),
            'sed_pais' => $sede->getSedPais(),
            'sed_direccion' => $sede->getSedDireccion(),
            'sed_ubicacion' => $sede->getSedUbicacion(),
        ];
    }

    // Función para obtener todas las sedes de una empresa (sedes únicas vinculadas al grupo "General")
    public function obtenerSedes(int $empresaId): array
    {
        // Buscar la empresa por su ID
        $empresa = $this->entityManager->getRepository(Empresa::class)->find($empresaId);
        if (!$empresa) {
            throw new \Exception('empresa no encontrada.');
        }

        // Buscar todas las configuraciones de asistencia asociadas a la empresa
        $configuracionesAsistencia = $this->entityManager->getRepository(ConfiguracionAsistencia::class)->createQueryBuilder('ca')
            ->join('ca.grupo', 'g')
            ->where('g.empresa = :empresa')
            ->setParameter('empresa', $empresa)
            ->getQuery()
            ->getResult();

        $sedesUnicas = [];

        foreach ($configuracionesAsistencia as $configuracion) {
            $sede = $configuracion->getSede();

            if ($sede && !$sede->getSedEliminado() && !in_array($sede->getId(), array_column($sedesUnicas, 'sed_id'))) {
                $sedesUnicas[] = [
                    'sed_id' => $sede->getId(),
                    'sed_nombre' => $sede->getSedNombre(),
                    'sed_pais' => $sede->getSedPais(),
                    'sed_direccion' => $sede->getSedDireccion(),
                    'sed_ubicacion' => $sede->getSedUbicacion(),
                ];
            }
        }

        return $sedesUnicas;
    }

    // Función para editar los datos de una sede
    public function editarSede(int $sedeId, array $nuevosDatos): array
    {
        // Buscar la sede por su ID
        $sede = $this->entityManager->getRepository(Sede::class)->find($sedeId);
        if (!$sede) {
            throw new \Exception('sede no encontrada.');
        }

        // Actualizar los datos de la sede
        $sede->setSedNombre($nuevosDatos['sed_nombre'] ?? $sede->getSedNombre());
        $sede->setSedPais($nuevosDatos['sed_pais'] ?? $sede->getSedPais());
        $sede->setSedDireccion($nuevosDatos['sed_direccion'] ?? $sede->getSedDireccion());
        $sede->setSedUbicacion($nuevosDatos['sed_ubicacion'] ?? $sede->getSedUbicacion());

        // Guardar los cambios en la base de datos
        $this->entityManager->flush();

        return [
            'sed_id' => $sede->getId(),
            'sed_nombre' => $sede->getSedNombre(),
            'sed_pais' => $sede->getSedPais(),
            'sed_direccion' => $sede->getSedDireccion(),
            'sed_ubicacion' => $sede->getSedUbicacion(),
        ];
    }

    // Función para eliminar una sede (cambio de estado lógico)
    public function borrarSede(int $sedeId): void
    {
        // Buscar la sede por su ID
        $sede = $this->entityManager->getRepository(Sede::class)->find($sedeId);
        if (!$sede) {
            throw new \Exception('sede no encontrada.');
        }

        // Marcar la sede como eliminada
        $sede->setSedEliminado(true);

        // Guardar los cambios en la base de datos
        $this->entityManager->flush();
    }
}
