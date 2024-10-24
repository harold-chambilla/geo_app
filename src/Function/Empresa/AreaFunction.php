<?php

namespace App\Function\Empresa;

use App\Entity\Area;
use App\Entity\ConfiguracionAsistencia;
use App\Entity\Empresa;
use App\Entity\Grupo;
use App\Entity\Puesto;
use App\Entity\Sede;
use Doctrine\ORM\EntityManagerInterface;

class AreaFunction
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Función para registrar un área y asociarla a un nuevo puesto "Sistema"
    public function registrarArea(int $empresaId, array $areaData): array
    {
        // Buscar la empresa por su ID
        $empresa = $this->entityManager->getRepository(Empresa::class)->find($empresaId);
        if (!$empresa) {
            throw new \Exception('empresa no encontrada');
        }

        // Buscar el grupo "General" de la empresa
        $grupo = $this->entityManager->getRepository(Grupo::class)->findOneBy([
            'empresa' => $empresa,
            'grp_nombre' => 'general',
        ]);
        if (!$grupo) {
            throw new \Exception('grupo "general" no encontrado en esta empresa');
        }

        // Verificar si ya existe un área con el mismo nombre vinculada a la empresa
        $areaExistente = $this->entityManager->getRepository(Area::class)->createQueryBuilder('a')
            ->join('a.puesto', 'p')
            ->join('p.configuracionAsistencias', 'ca')
            ->where('ca.grupo = :grupo')
            ->andWhere('a.araNombre = :nombre')
            ->andWhere('a.araEliminado = false')
            ->setParameter('grupo', $grupo)
            ->setParameter('nombre', $areaData['ara_nombre'])
            ->getQuery()
            ->getOneOrNullResult();

        if ($areaExistente) {
            throw new \Exception('ya existe un área con el mismo nombre en esta empresa');
        }

        // Buscar la configuración de asistencia con estado "Sistema" vinculada al grupo
        $configuracionSistema = $this->entityManager->getRepository(ConfiguracionAsistencia::class)->findOneBy([
            'grupo' => $grupo,
            'cas_estado' => 'sistema',
        ]);
        if (!$configuracionSistema) {
            throw new \Exception('configuración de asistencia con estado "sistema" no encontrada en este grupo');
        }

        // Obtener la sede principal a través de la configuración de asistencia con estado "Sistema"
        $sedePrincipal = $configuracionSistema->getSede();
        if (!$sedePrincipal) {
            throw new \Exception('sede principal no encontrada para la empresa');
        }

        // Crear la nueva área
        $area = new Area();
        $area->setAraNombre($areaData['ara_nombre']);
        $area->setAraEliminado(false);

        $this->entityManager->persist($area);

        // Crear un nuevo puesto "Sistema" para el área y la configuración de asistencia
        $puestoSistema = new Puesto();
        $puestoSistema->setPstNombre('sistema');
        $puestoSistema->setPstEliminado(false);
        $puestoSistema->setArea($area);

        $this->entityManager->persist($puestoSistema);

        // Crear la configuración de asistencia relacionada con el área
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
        $configuracionAsistencia->setCasEstado('area');
        $configuracionAsistencia->setGrupo($grupo);
        $configuracionAsistencia->setSede($sedePrincipal);
        $configuracionAsistencia->setPuesto($puestoSistema);
        
        $this->entityManager->persist($configuracionAsistencia);
        $this->entityManager->flush();

        return [
            'ara_id' => $area->getId(),
            'ara_nombre' => $area->getAraNombre(),
            'sede_principal' => $sedePrincipal->getSedNombre(),
            'puesto_sistema' => $puestoSistema->getPstNombre(),
        ];
    }

    // Función para obtener todas las áreas vinculadas a la empresa (a través de puestos y configuraciones de asistencia)
    public function obtenerAreas(int $empresaId): array
    {
        // Buscar la empresa por su ID
        $empresa = $this->entityManager->getRepository(Empresa::class)->find($empresaId);
        if (!$empresa) {
            throw new \Exception('empresa no encontrada');
        }

        // Buscar todas las áreas asociadas a la empresa a través de puestos y configuraciones de asistencia
        $areas = $this->entityManager->getRepository(Area::class)->createQueryBuilder('a')
            ->join('a.puesto', 'p')
            ->join('p.configuracionAsistencias', 'ca')
            ->join('ca.grupo', 'g')
            ->where('g.empresa = :empresa')
            ->andWhere('a.ara_eliminado = false')
            ->setParameter('empresa', $empresa)
            ->getQuery()
            ->getResult();

        // Usar un array para almacenar áreas únicas
        $areasUnicas = [];

        foreach ($areas as $area) {
            $areaId = $area->getId();
            if (!in_array($areaId, array_column($areasUnicas, 'ara_id'))) {
                $areasUnicas[] = [
                    'ara_id' => $area->getId(),
                    'ara_nombre' => $area->getAraNombre(),
                    'puestos' => [],
                ];

                // Obtener todos los puestos relacionados con el área
                foreach ($area->getPuestos() as $puesto) {
                    $areasUnicas[array_key_last($areasUnicas)]['puestos'][] = [
                        'pst_nombre' => $puesto->getPstNombre(),
                    ];
                }
            }
        }

        return $areasUnicas;
    }

    // Función para editar los datos de un área
    public function editarArea(int $areaId, array $nuevosDatos): array
    {
        // Buscar el área por su ID
        $area = $this->entityManager->getRepository(Area::class)->find($areaId);
        if (!$area) {
            throw new \Exception('area no encontrada');
        }

        // Actualizar los datos del área
        $area->setAraNombre($nuevosDatos['ara_nombre'] ?? $area->getAraNombre());

        // Guardar los cambios en la base de datos
        $this->entityManager->flush();

        return [
            'ara_id' => $area->getId(),
            'ara_nombre' => $area->getAraNombre(),
        ];
    }

    // Función para eliminar un área (cambio de estado lógico)
    public function borrarArea(int $areaId): void
    {
        // Buscar el área por su ID
        $area = $this->entityManager->getRepository(Area::class)->find($areaId);
        if (!$area) {
            throw new \Exception('area no encontrada');
        }

        // Marcar el área como eliminada
        $area->setAraEliminado(true);

        // Guardar los cambios en la base de datos
        $this->entityManager->flush();
    }
}

