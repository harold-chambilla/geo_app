<?php 

namespace App\Function\Colaborador;

use App\Entity\Colaborador;
use App\Entity\ConfiguracionAsistencia;
use App\Entity\Grupo;
use App\Entity\HorarioTrabajo;
use Doctrine\ORM\EntityManagerInterface;

class HorarioTrabajoFunction
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ){}

    public function obtenerHorarioPorColaborador(int $colaboradorId, \DateTime $fecha): array
    {
        // 1. Buscar el colaborador por ID
        $colaborador = $this->entityManager->getRepository(Colaborador::class)->findOneBy([
            'id' => $colaboradorId
        ]);
        if (!$colaborador) {
            throw new \Exception('Colaborador no encontrado');
        }

        // 2. Obtener la empresa del colaborador
        $empresa = $colaborador->getGrupo()->getEmpresa();
        if (!$empresa) {
            throw new \Exception('Empresa del colaborador no encontrada');
        }

        // 3. Obtener los grupos asociados a la empresa
        $grupos = $this->entityManager->getRepository(Grupo::class)->findBy(['empresa' => $empresa]);
        if (empty($grupos)) {
            throw new \Exception('No se encontraron grupos asociados a la empresa');
        }

        // 4. Buscar la configuración de asistencia con estado "sistema" en los grupos de la empresa
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('ca')
            ->from(ConfiguracionAsistencia::class, 'ca')
            ->where('ca.grupo IN (:grupos)')
            ->andWhere('ca.cas_estado = :estado')
            ->setParameter('grupos', $grupos)
            ->setParameter('estado', 'sistema')
            ->setMaxResults(1); // Tomar solo una configuración

        $configuracionSistema = $qb->getQuery()->getOneOrNullResult();

        if (!$configuracionSistema) {
            throw new \Exception('No se encontró configuración de asistencia con estado "sistema" para la empresa');
        }

        // 5. Buscar el horario laboral exacto del colaborador para la fecha proporcionada
        $horario = $this->entityManager->getRepository(HorarioTrabajo::class)->findOneBy([
            'colaborador' => $colaborador,
            'hot_fecha' => $fecha, // Se asume que la tabla tiene un campo "hor_fecha" con la fecha exacta
            'hot_eliminado' => false,
        ]);

        if (!$horario) {
            throw new \Exception('No se encontró horario de trabajo para la fecha indicada');
        }

        // 6. Retornar los datos en formato array
        return [
            'horario' => [
                'hot_id' => $horario->getId(),
                'hot_entrada' => $horario->getHotHoraentrada()->format('H:i:s'),
                'hot_salida' => $horario->getHotHorasalida()->format('H:i:s'),
                'hot_fecha' => $horario->getHotFecha()->format('Y-m-d'),
                'hot_diasemana' => $horario->getHotDiasemana(),
                'hot_tipojornada' => $horario->getHotTipojornada(),
                'hot_eliminado' => $horario->isHotEliminado(),
                'hot_descanso' => $horario->getHotDescanso(),
                'colaborador' => [
                    'id' => $colaborador->getId(),
                    'nombre' => $colaborador->getColNombres(),
                    'apellido' => $colaborador->getColApellidos(),
                    'dni' => $colaborador->getColDninit(),
                ],
            ],
            'configuracion_asistencia' => [
                'cas_id' => $configuracionSistema->getId(),
                'cas_tiempo_falta_horas' => $configuracionSistema->getCasTiempoFaltaHoras(),
                'cas_tolerancia_ingreso_minutos' => $configuracionSistema->getCasToleranciaIngresoMinutos(),
                'cas_permitir_foto' => $configuracionSistema->isCasPermitirFoto(),
                'cas_faltas_tardanzas' => $configuracionSistema->isCasFaltasTardanzas(),
                'cas_permisos' => $configuracionSistema->isCasPermisos(),
                'cas_vacaciones' => $configuracionSistema->isCasVacaciones(),
                'cas_marcacion' => $configuracionSistema->isCasMarcacion(),
                'cas_modalidad' => $configuracionSistema->getCasModalidad(),
                'cas_area' => $configuracionSistema->isCasArea(),
                'cas_puesto' => $configuracionSistema->isCasPuesto(),
                'cas_predhorario' => $configuracionSistema->isCasPredhorario(),
                'cas_eliminado' => $configuracionSistema->isCasEliminado(),
                'cas_estado' => $configuracionSistema->getCasEstado(),
                'cas_horasextras' => $configuracionSistema->isCasHorasextras(),
                'grupo' => [
                    'id' => $configuracionSistema->getGrupo()->getId(),
                    'nombre' => $configuracionSistema->getGrupo()->getGrpNombre(),
                ],
                'sede' => [
                    'id' => $configuracionSistema->getSede()->getId(),
                    'nombre' => $configuracionSistema->getSede()->getSedNombre(),
                ],
                'puesto' => [
                    'id' => $configuracionSistema->getPuesto()->getId(),
                    'nombre' => $configuracionSistema->getPuesto()->getPstNombre(),
                ]
            ],
        ];
    }  
}
