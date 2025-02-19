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
            ->leftJoin('a.colaborador', 'c') // 🔗 Relación con Colaborador
            ->leftJoin('c.grupo', 'g') // 🔗 Relación con Grupo
            ->leftJoin('App\Entity\ConfiguracionAsistencia', 'ca', 'WITH', 'ca.grupo = g.id') // 🔗 Configuración Asistencia asociada al Grupo
            ->leftJoin('ca.puesto', 'p') // 🔗 Relación con Puesto
            ->leftJoin('p.area', 'ar') // 🔗 Relación con Área
            ->where('a.colaborador IN (:colaboradores)')
            ->setParameter('colaboradores', $colaboradorIds)
            ->orderBy('a.asi_fechaentrada', 'DESC')
            ->getQuery()
            ->getResult();
    
        $asistenciasArray = array_map(function ($asistencia) {
            $colaborador = $asistencia->getColaborador();
            $grupo = $colaborador->getGrupo();
            $configAsistencia = $this->entityManager->getRepository('App\Entity\ConfiguracionAsistencia')->findOneBy(['grupo' => $grupo]);
            $puesto = $configAsistencia?->getPuesto();
            $area = $puesto?->getArea()?->getAraNombre() ?? 'No asignado';
    
            return [
                'id' => $asistencia->getId(),
                'colaborador_id' => $colaborador->getId(),
                'colaborador' => $colaborador->getColNombres() . ' ' . $colaborador->getColApellidos(),
                'area' => $area,
                'puesto' => $puesto?->getPstNombre() ?? 'No asignado',
                'modalidad' => $configAsistencia?->getCasModalidad() ?? 'No registrado',
                'fecha_entrada' => $asistencia->getAsiFechaentrada()->format('Y-m-d'),
                'fecha_salida' => $asistencia->getAsiFechasalida()?->format('Y-m-d'),
                'hora_entrada' => $asistencia->getAsiHoraentrada()?->format('H:i:s'),
                'hora_salida' => $asistencia->getAsiHorasalida()?->format('H:i:s'),
                'estado_entrada' => $asistencia->getAsiEstadoentrada(),
                'estado_salida' => $asistencia->getAsiEstadosalida(),
                'notas' => $asistencia->getAsiNotas(),
                'eliminado' => $asistencia->isAsiEliminado(),
            ];
        }, $asistencias);
    
        return new JsonResponse($asistenciasArray);
    }    
}