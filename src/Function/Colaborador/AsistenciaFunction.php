<?php 

namespace App\Function\Colaborador;

use App\Entity\Asistencia;
use App\Entity\Colaborador;
use Doctrine\ORM\EntityManagerInterface;

class AsistenciaFunction
{
    public function __construct(private EntityManagerInterface $entityManager){}

    public function crearAsistencia(array $data): array
    {
        $colaborador = $this->entityManager->getRepository(Colaborador::class)->find($data['colaborador_id']);

        if (!$colaborador) {
            return ['error' => 'Colaborador no encontrado'];
        }

        $asistencia = new \App\Entity\Asistencia();
        $asistencia->setAsiFechaentrada(new \DateTime($data['asi_fechaentrada']))
            ->setAsiFechasalida($data['asi_fechasalida'] !== null ? new \DateTime($data['asi_fechasalida']) : null)
            ->setAsiHoraentrada(new \DateTime($data['asi_horaentrada']))
            ->setAsiHorasalida($data['asi_horasalida'] !== null ? new \DateTime($data['asi_horasalida']) : null)
            ->setAsiFotoentrada($data['asi_fotoentrada'])
            ->setAsiFotosalida($data['asi_fotosalida'])
            ->setAsiUbicacionentrada($data['asi_ubicacionentrada'])
            ->setAsiUbicacionsalida($data['asi_ubicacionsalida'])
            ->setAsiEstadoentrada($data['asi_estadoentrada'])
            ->setAsiEstadosalida($data['asi_estadosalida'])
            ->setAsiNotas($data['asi_notas'])
            ->setAsiEliminado($data['asi_eliminado'])
            ->setColaborador($colaborador);

        $this->entityManager->persist($asistencia);
        $this->entityManager->flush();

        return $this->formatAsistencia($asistencia);
    }
    
    public function obtenerAsistencia(array $criteria): array
    {
        $repository = $this->entityManager->getRepository(Asistencia::class);

        // Obtener asistencia por ID
        if (isset($criteria['id'])) {
            $asistencia = $repository->find($criteria['id']);
            if (!$asistencia) {
                return ['error' => 'Asistencia no encontrada'];
            }
            return $this->formatAsistencia($asistencia);
        }

        // Crear consulta usando las funciones de Symfony
        $qb = $repository->createQueryBuilder('a');

        if (isset($criteria['colaborador_id'])) {
            $qb->andWhere('a.colaborador = :colaborador_id')
                ->setParameter('colaborador_id', $criteria['colaborador_id']);
        }

        if (isset($criteria['fecha'])) {
            $fechaInicio = new \DateTime($criteria['fecha']);
            $fechaFin = clone $fechaInicio;
            $fechaFin->modify('+1 day');

            $qb->andWhere('a.asi_fechaentrada BETWEEN :fecha_inicio AND :fecha_fin')
                ->setParameter('fecha_inicio', $fechaInicio->format('Y-m-d 00:00:00'))
                ->setParameter('fecha_fin', $fechaFin->format('Y-m-d 23:59:59'));
        }

        $asistencias = $qb->getQuery()->getResult();

        if (!$asistencias) {
            return ['error' => 'No se encontraron asistencias'];
        }

        return array_map([$this, 'formatAsistencia'], $asistencias);
    }

    public function eliminarAsistencia(int $id): array
    {
        $repository = $this->entityManager->getRepository(Asistencia::class);
        $asistencia = $repository->find($id);

        if (!$asistencia) {
            return ['error' => 'Asistencia no encontrada'];
        }

        $asistencia->setAsiEliminado(true);

        $this->entityManager->persist($asistencia);
        $this->entityManager->flush();

        return ['success' => 'Asistencia eliminada correctamente', 'id' => $asistencia->getId()];
    }

    private function formatAsistencia(Asistencia $asistencia): array
    {
        return [
            'id' => $asistencia->getId(),
            'asi_fechaentrada' => $asistencia->getAsiFechaentrada()->format('Y-m-d H:i:s'),
            'asi_fechasalida' => $asistencia->getAsiFechasalida() ? $asistencia->getAsiFechasalida()->format('Y-m-d H:i:s') : null,
            'asi_horaentrada' => $asistencia->getAsiHoraentrada()->format('H:i:s'),
            'asi_horasalida' => $asistencia->getAsiHorasalida() ? $asistencia->getAsiHorasalida()->format('H:i:s') : null,
            'asi_fotoentrada' => $asistencia->getAsiFotoentrada(),
            'asi_fotosalida' => $asistencia->getAsiFotosalida(),
            'asi_ubicacionentrada' => $asistencia->getAsiUbicacionentrada(),
            'asi_ubicacionsalida' => $asistencia->getAsiUbicacionsalida(),
            'asi_estadoentrada' => $asistencia->getAsiEstadoentrada(),
            'asi_estadosalida' => $asistencia->getAsiEstadosalida(),
            'asi_notas' => $asistencia->getAsiNotas(),
            'asi_eliminado' => $asistencia->isAsiEliminado(),
            'colaborador_id' => $asistencia->getColaborador()->getId(),
        ];
    }

    public function actualizarAsistencia(int $id, array $data): array
    {
        $repository = $this->entityManager->getRepository(Asistencia::class);
        $asistencia = $repository->find($id);

        if (!$asistencia) {
            return ['error' => 'Asistencia no encontrada'];
        }

        if (isset($data['asi_fechaentrada'])) {
            $asistencia->setAsiFechaentrada(new \DateTime($data['asi_fechaentrada']));
        }

        if (isset($data['asi_fechasalida'])) {
            $asistencia->setAsiFechasalida(new \DateTime($data['asi_fechasalida']));
        }

        if (isset($data['asi_horaentrada'])) {
            $asistencia->setAsiHoraentrada(new \DateTime($data['asi_horaentrada']));
        }

        if (isset($data['asi_horasalida'])) {
            $asistencia->setAsiHorasalida(new \DateTime($data['asi_horasalida']));
        }

        if (isset($data['asi_fotoentrada'])) {
            $asistencia->setAsiFotoentrada($data['asi_fotoentrada']);
        }

        if (isset($data['asi_fotosalida'])) {
            $asistencia->setAsiFotosalida($data['asi_fotosalida']);
        }

        if (isset($data['asi_ubicacionentrada'])) {
            $asistencia->setAsiUbicacionentrada($data['asi_ubicacionentrada']);
        }

        if (isset($data['asi_ubicacionsalida'])) {
            $asistencia->setAsiUbicacionsalida($data['asi_ubicacionsalida']);
        }

        if (isset($data['asi_estadoentrada'])) {
            $asistencia->setAsiEstadoentrada($data['asi_estadoentrada']);
        }

        if (isset($data['asi_estadosalida'])) {
            $asistencia->setAsiEstadosalida($data['asi_estadosalida']);
        }

        if (isset($data['asi_notas'])) {
            $asistencia->setAsiNotas($data['asi_notas']);
        }

        if (isset($data['asi_eliminado'])) {
            $asistencia->setAsiEliminado($data['asi_eliminado']);
        }

        if (isset($data['colaborador_id'])) {
            $colaborador = $this->entityManager->getRepository(Colaborador::class)->find($data['colaborador_id']);
            if (!$colaborador) {
                return ['error' => 'Colaborador no encontrado'];
            }
            $asistencia->setColaborador($colaborador);
        }

        $this->entityManager->persist($asistencia);
        $this->entityManager->flush();

        return $this->formatAsistencia($asistencia);
    }
    }