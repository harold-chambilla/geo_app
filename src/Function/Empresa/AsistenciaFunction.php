<?php

namespace App\Function\Empresa;

use App\Entity\Asistencia;
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
            ->where('a.colaborador IN (:colaboradores)')
            ->setParameter('colaboradores', $colaboradorIds)
            ->orderBy('a.asi_fechaentrada', 'DESC')
            ->getQuery()
            ->getResult();

        $asistenciasArray = array_map(fn($asistencia) => [
            'id' => $asistencia->getId(),
            'colaborador' => $asistencia->getColaborador()->getId(),
            'fecha_entrada' => $asistencia->getAsiFechaentrada()->format('Y-m-d H:i:s'),
            'fecha_salida' => $asistencia->getAsiFechasalida()?->format('Y-m-d H:i:s'),
            'hora_entrada' => $asistencia->getAsiHoraentrada()?->format('H:i:s'),
            'hora_salida' => $asistencia->getAsiHorasalida()?->format('H:i:s'),
            'foto_entrada' => $asistencia->getAsiFotoentrada(),
            'foto_salida' => $asistencia->getAsiFotosalida(),
            'ubicacion_entrada' => $asistencia->getAsiUbicacionentrada(),
            'ubicacion_salida' => $asistencia->getAsiUbicacionsalida(),
            'estado_entrada' => $asistencia->getAsiEstadoentrada(),
            'estado_salida' => $asistencia->getAsiEstadosalida(),
            'notas' => $asistencia->getAsiNotas(),
            'eliminado' => $asistencia->isAsiEliminado(),
        ], $asistencias);

        return new JsonResponse($asistenciasArray);
    }
}