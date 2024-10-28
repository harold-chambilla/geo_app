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

    public function registrarArea(int $empresaId, array $areaData): array
    {
        // Buscar la empresa por su ID
        $empresa = $this->entityManager->getRepository(Empresa::class)->find($empresaId);
        if (!$empresa) {
            throw new \Exception('empresa no encontrada');
        }

        // Asegurarse de que el grupo "general" existe en la empresa, o crearlo si no existe
        $grupo = $this->entityManager->getRepository(Grupo::class)->findOneBy([
            'empresa' => $empresa,
            'grp_nombre' => 'general',
        ]);

        if (!$grupo) {
            // Crear el grupo "general" si no se encuentra
            $grupo = new Grupo();
            $grupo->setGrpNombre('general');
            $grupo->setGrpDescripcion('Grupo general creado automáticamente');
            $grupo->setGrpEliminado(false);
            $grupo->setEmpresa($empresa);
            $this->entityManager->persist($grupo);
        }

        // Verificar si ya existe un área con el mismo nombre en la empresa
        $areaExistente = $this->entityManager->getRepository(Area::class)->createQueryBuilder('a')
            ->join('a.puestos', 'p')
            ->join('p.configuracionAsistencias', 'ca')
            ->join('ca.grupo', 'g')
            ->where('g.empresa = :empresa')
            ->andWhere('a.ara_nombre = :nombre')
            ->andWhere('a.ara_eliminado = false')
            ->setParameter('empresa', $empresa)
            ->setParameter('nombre', $areaData['ara_nombre'])
            ->getQuery()
            ->getOneOrNullResult();

        if ($areaExistente) {
            throw new \Exception('ya existe un área con el mismo nombre en esta empresa');
        }

        // Buscar la sede principal asociada a cualquier configuración de asistencia en estado "sistema"
        $sedePrincipal = $this->entityManager->getRepository(Sede::class)->createQueryBuilder('s')
            ->join('s.configuracionAsistencias', 'ca')
            ->join('ca.grupo', 'g')
            ->where('g.empresa = :empresa')
            ->andWhere('ca.cas_estado = :estado')
            ->setParameter('empresa', $empresa)
            ->setParameter('estado', 'sistema')
            ->getQuery()
            ->getOneOrNullResult();

        if (!$sedePrincipal) {
            throw new \Exception('sede principal no encontrada para la empresa');
        }

        // Crear la nueva área
        $area = new Area();
        $area->setAraNombre($areaData['ara_nombre']);
        $area->setAraEliminado(false);

        $this->entityManager->persist($area);

        // Crear un nuevo puesto "Sistema" para el área
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
            ->join('a.puestos', 'p') // Relación en Puesto con Area
            ->join('p.configuracionAsistencias', 'ca') // Relación en Configuración de Asistencia con Puesto
            ->join('ca.grupo', 'g') // Relación en Grupo con Configuración de Asistencia
            ->where('g.empresa = :empresa') // Filtrar por empresa a través del grupo
            ->andWhere('a.ara_eliminado = false') // Solo áreas no eliminadas
            ->setParameter('empresa', $empresa)
            ->getQuery()
            ->getResult();

        // Usar un array para almacenar áreas únicas
        $areasUnicas = [];

        foreach ($areas as $area) {
            $areaId = $area->getId();

            if (!in_array($areaId, array_column($areasUnicas, 'ara_id'))) {
                $areaData = [
                    'ara_id' => $area->getId(),
                    'ara_nombre' => $area->getAraNombre(),
                    'puestos' => [],
                ];

                // Obtener todos los puestos relacionados con el área
                foreach ($area->getPuestos() as $puesto) {
                    if (!$puesto->isPstEliminado()) { // Solo incluir puestos no eliminados
                        $areaData['puestos'][] = [
                            'pst_id' => $puesto->getId(),
                            'pst_nombre' => $puesto->getPstNombre(),
                        ];
                    }
                }

                $areasUnicas[] = $areaData;
            }
        }

        return $areasUnicas;
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

    // Función para obtener un área por su ID
    public function obtenerAreaPorId(int $areaId): array
    {
        // Buscar el área por su ID
        $area = $this->entityManager->getRepository(Area::class)->find($areaId);
        if (!$area || $area->isAraEliminado()) {
            throw new \Exception('Área no encontrada o está eliminada');
        }

        // Preparar la estructura de datos del área
        $areaData = [
            'ara_id' => $area->getId(),
            'ara_nombre' => $area->getAraNombre(),
            'puestos' => [],
        ];

        // Obtener todos los puestos relacionados con el área
        foreach ($area->getPuestos() as $puesto) {
            if (!$puesto->isPstEliminado()) {
                $areaData['puestos'][] = [
                    'pst_id' => $puesto->getId(),
                    'pst_nombre' => $puesto->getPstNombre(),
                ];
            }
        }

        return $areaData;
    }
}

