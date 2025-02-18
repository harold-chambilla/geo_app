<?php

namespace App\Function\Empresa;

use App\Entity\Asistencia;
use App\Entity\HorarioTrabajo;
use App\Entity\Colaborador;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AsistenciaFunction
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function obtenerAsistencias(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['colaboradores']) || !is_array($data['colaboradores'])) {
            return new JsonResponse(['error' => 'Lista de colaboradores no proporcionada o inválida.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $colaboradorIds = $data['colaboradores'];

        $asistencias = $this->entityManager->getRepository(Asistencia::class)
            ->createQueryBuilder('a')
            ->leftJoin('a.colaborador', 'c')
            ->leftJoin('c.grupo', 'g')
            ->leftJoin('g.puesto', 'p')
            ->leftJoin('g.area', 'ar')
            ->where('a.colaborador IN (:colaboradores)')
            ->setParameter('colaboradores', $colaboradorIds)
            ->orderBy('a.asi_fechaentrada', 'DESC')
            ->getQuery()
            ->getResult();

        $asistenciasArray = array_map(function ($asistencia) {
            $colaborador = $asistencia->getColaborador();
            $fechaEntrada = $asistencia->getAsiFechaentrada();

            // Obtener modalidad del horario de trabajo del colaborador en esa fecha
            $horario = $this->entityManager->getRepository(HorarioTrabajo::class)
                ->findOneBy([
                    'colaborador' => $colaborador,
                    'hot_fecha' => $fechaEntrada
                ]);

            // Obtener área y puesto del colaborador si existen
            $grupo = $colaborador->getGrupo();
            $puesto = $grupo?->getPuesto()?->getPstNombre() ?? 'No asignado';
            $area = $grupo?->getArea()?->getAraNombre() ?? 'No asignado';

            return [
                'id' => $asistencia->getId(),
                'colaborador_id' => $colaborador->getId(),
                'colaborador' => $colaborador->getColNombres() . ' ' . $colaborador->getColApellidos(),
                'area' => $area,
                'puesto' => $puesto,
                'fecha_entrada' => $fechaEntrada->format('Y-m-d'),
                'fecha_salida' => $asistencia->getAsiFechasalida()?->format('Y-m-d'),
                'hora_entrada' => $asistencia->getAsiHoraentrada()?->format('H:i:s'),
                'hora_salida' => $asistencia->getAsiHorasalida()?->format('H:i:s'),
                'foto_entrada' => $asistencia->getAsiFotoentrada(),
                'foto_salida' => $asistencia->getAsiFotosalida(),
                'ubicacion_entrada' => $asistencia->getAsiUbicacionentrada(),
                'ubicacion_salida' => $asistencia->getAsiUbicacionsalida(),
                'estado_entrada' => $asistencia->getAsiEstadoentrada(),
                'estado_salida' => $asistencia->getAsiEstadosalida(),
                'modalidad' => $horario ? $horario->getHotTipojornada() : 'No registrado',
                'notas' => $asistencia->getAsiNotas(),
                'eliminado' => $asistencia->isAsiEliminado(),
            ];
        }, $asistencias);

        return new JsonResponse($asistenciasArray);
    }
}