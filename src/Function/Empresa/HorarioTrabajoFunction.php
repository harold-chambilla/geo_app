<?php

namespace App\Function\Empresa;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\HorarioTrabajo;
use App\Entity\Colaborador;

class HorarioTrabajoFunction
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function registrarHorario(array $data): array
    {
        $tipo_registro = $data['tipo_registro'] ?? 'individual'; // Puede ser 'mes', 'semana', 'dia', 'colaborador'
        $colaborador_ids = $data['colaborador_ids'] ?? [];
        $dia_inicio = $data['dia_inicio'] ?? null;
        $dia_fin = $data['dia_fin'] ?? null;
        $fecha = $data['fecha'] ?? null;
        $hora_entrada = $data['hora_entrada'];
        $hora_salida = $data['hora_salida'];
        $descanso = $data['descanso'] ?? 'no establecido';
        $tipo_jornada = $data['tipo_jornada'] ?? 'presencial';
        $aplicar_a_todo = $data['aplicar_a_todo'] ?? false;

        if (empty($colaborador_ids)) {
            throw new \InvalidArgumentException('debe enviarse al menos un id de colaborador.');
        }

        return match ($tipo_registro) {
            'mes' => $this->registrarPorMes($colaborador_ids, $hora_entrada, $hora_salida, $dia_inicio, $dia_fin, $descanso, $tipo_jornada),
            'semana' => $this->registrarPorSemana($colaborador_ids, $hora_entrada, $hora_salida, $dia_inicio, $dia_fin, $descanso, $tipo_jornada),
            'dia' => $this->registrarPorDia($colaborador_ids, $hora_entrada, $hora_salida, $fecha, $tipo_jornada),
            'colaborador' => $this->registrarPorColaborador($colaborador_ids, $fecha, $hora_entrada, $hora_salida, $descanso, $tipo_jornada, $aplicar_a_todo),
            default => [],
        };
    }

    private function registrarPorMes(array $colaborador_ids, string $hora_entrada, string $hora_salida, string $dia_inicio, string $dia_fin, string $descanso, string $tipo_jornada): array
    {
        $colaboradores = $this->obtenerColaboradores($colaborador_ids);
        $fecha_inicio = new \DateTime($dia_inicio);
        $fecha_fin = new \DateTime($dia_fin);
        $registros = [];

        while ($fecha_inicio <= $fecha_fin) {
            foreach ($colaboradores as $colaborador) {
                $tipo_jornada_aplicada = $fecha_inicio->format('l') === ucfirst($descanso) ? 'descanso' : $tipo_jornada;

                if (!$this->existeHorario($colaborador, $fecha_inicio)) {
                    $registros[] = $this->crearHorario($colaborador, clone $fecha_inicio, $hora_entrada, $hora_salida, $descanso, $tipo_jornada_aplicada);
                }
            }
            $fecha_inicio->modify('+1 day');
        }

        return $registros;
    }

    private function registrarPorSemana(array $colaborador_ids, string $hora_entrada, string $hora_salida, string $dia_inicio, string $dia_fin, string $descanso, string $tipo_jornada): array
    {
        $colaboradores = $this->obtenerColaboradores($colaborador_ids);
        $fecha_inicio = new \DateTime($dia_inicio);
        $fecha_fin = new \DateTime($dia_fin);
        $registros = [];

        while ($fecha_inicio <= $fecha_fin) {
            foreach ($colaboradores as $colaborador) {
                $tipo_jornada_aplicada = $fecha_inicio->format('l') === ucfirst($descanso) ? 'descanso' : $tipo_jornada;

                if (!$this->existeHorario($colaborador, $fecha_inicio)) {
                    $registros[] = $this->crearHorario($colaborador, clone $fecha_inicio, $hora_entrada, $hora_salida, $descanso, $tipo_jornada_aplicada);
                }
            }
            $fecha_inicio->modify('+1 day');
        }

        return $registros;
    }

    private function registrarPorDia(array $colaborador_ids, string $hora_entrada, string $hora_salida, string $fecha, string $tipo_jornada): array
    {
        $colaboradores = $this->obtenerColaboradores($colaborador_ids);
        $fecha_registro = new \DateTime($fecha);
        $registros = [];

        foreach ($colaboradores as $colaborador) {
            if (!$this->existeHorario($colaborador, $fecha_registro)) {
                $registros[] = $this->crearHorario($colaborador, $fecha_registro, $hora_entrada, $hora_salida, 'no establecido', $tipo_jornada);
            }
        }

        return $registros;
    }

    private function registrarPorColaborador(array $colaborador_ids, ?string $fecha, string $hora_entrada, string $hora_salida, string $descanso, string $tipo_jornada, bool $aplicar_a_todo): array
    {
        $colaboradores = $this->obtenerColaboradores($colaborador_ids);
        $registros = [];
        $fecha_horario = $fecha ? new \DateTime($fecha) : new \DateTime();

        foreach ($colaboradores as $colaborador) {
            if ($aplicar_a_todo) {
                for ($i = 0; $i < 7; $i++) {
                    $dia_semana = (new \DateTime("this week"))->modify("+{$i} days");
                    $tipo_jornada_aplicada = $dia_semana->format('l') === ucfirst($descanso) ? 'descanso' : $tipo_jornada;

                    if (!$this->existeHorario($colaborador, $dia_semana)) {
                        $registros[] = $this->crearHorario($colaborador, $dia_semana, $hora_entrada, $hora_salida, $descanso, $tipo_jornada_aplicada);
                    }
                }
            } else {
                $tipo_jornada_aplicada = $fecha_horario->format('l') === ucfirst($descanso) ? 'descanso' : $tipo_jornada;

                if (!$this->existeHorario($colaborador, $fecha_horario)) {
                    $registros[] = $this->crearHorario($colaborador, $fecha_horario, $hora_entrada, $hora_salida, $descanso, $tipo_jornada_aplicada);
                }
            }
        }

        return $registros;
    }

    private function obtenerColaboradores(array $colaborador_ids): array
    {
        $colaboradores = $this->entityManager->getRepository(Colaborador::class)->findBy(['id' => $colaborador_ids, 'col_eliminado' => false]);

        if (empty($colaboradores)) {
            throw new \Exception('no se encontraron colaboradores con los ids proporcionados.');
        }

        return $colaboradores;
    }

    private function existeHorario(Colaborador $colaborador, \DateTime $fecha): bool
    {
        $horario_existente = $this->entityManager->getRepository(HorarioTrabajo::class)->findOneBy([
            'colaborador' => $colaborador,
            'hot_fecha' => $fecha,
            'hot_eliminado' => false,
        ]);

        return $horario_existente !== null;
    }

    private function crearHorario(Colaborador $colaborador, \DateTime $fecha, string $hora_entrada, string $hora_salida, string $descanso, string $tipo_jornada): array
    {
        $horario = new HorarioTrabajo();
        $horario->setColaborador($colaborador);
        $horario->setHotFecha($fecha);
        $horario->setHotHoraentrada(new \DateTime($hora_entrada));
        $horario->setHotHorasalida(new \DateTime($hora_salida));
        $horario->setHotDiasemana($fecha->format('l'));
        $horario->setHotTipojornada($tipo_jornada);
        $horario->setHotDescanso($descanso);
        $horario->setHotEliminado(false);

        $this->entityManager->persist($horario);
        $this->entityManager->flush();

        return [
            'id' => $horario->getId(),
            'colaborador_id' => $colaborador->getId(),
            'fecha' => $horario->getHotFecha()->format('Y-m-d'),
            'hora_entrada' => $horario->getHotHoraentrada()->format('H:i'),
            'hora_salida' => $horario->getHotHorasalida()->format('H:i'),
            'tipo_jornada' => $horario->getHotTipojornada(),
            'descanso' => $horario->getHotDescanso(),
        ];
    }

    public function modificarHorario(int $horario_id, array $data): array
    {
        $horario = $this->entityManager->getRepository(HorarioTrabajo::class)->find($horario_id);

        if (!$horario || $horario->isHotEliminado()) {
            throw new \Exception('el horario especificado no existe o ha sido eliminado.');
        }

        $descanso = $data['descanso'] ?? $horario->getHotDescanso();
        $tipo_jornada = $data['tipo_jornada'] ?? $horario->getHotTipojornada();
        $tipo_jornada_aplicada = $horario->getHotFecha()->format('l') === ucfirst($descanso) ? 'descanso' : $tipo_jornada;

        if (isset($data['hora_entrada'])) {
            $horario->setHotHoraentrada(new \DateTime($data['hora_entrada']));
        }

        if (isset($data['hora_salida'])) {
            $horario->setHotHorasalida(new \DateTime($data['hora_salida']));
        }

        $horario->setHotTipojornada($tipo_jornada_aplicada);
        $horario->setHotDescanso($descanso);

        $this->entityManager->flush();

        return [
            'id' => $horario->getId(),
            'colaborador_id' => $horario->getColaborador()->getId(),
            'fecha' => $horario->getHotFecha()->format('Y-m-d'),
            'hora_entrada' => $horario->getHotHoraentrada()->format('H:i'),
            'hora_salida' => $horario->getHotHorasalida()->format('H:i'),
            'tipo_jornada' => $horario->getHotTipojornada(),
            'descanso' => $horario->getHotDescanso(),
        ];
    }

    public function eliminarHorarioLogico(int $horario_id): array
    {
        $horario = $this->entityManager->getRepository(HorarioTrabajo::class)->find($horario_id);

        if (!$horario) {
            throw new \Exception('el horario especificado no existe.');
        }

        $horario->setHotEliminado(true);
        $this->entityManager->flush();

        return ['status' => 'success', 'message' => 'horario eliminado lógicamente.'];
    }

    public function obtenerHorarios(array $colaborador_ids): array
    {
        $horarios = $this->entityManager->getRepository(HorarioTrabajo::class)
            ->createQueryBuilder('h')
            ->where('h.colaborador IN (:colaborador_ids)')
            ->andWhere('h.hot_eliminado = false')
            ->setParameter('colaborador_ids', $colaborador_ids)
            ->getQuery()
            ->getResult();

        return array_map(function (HorarioTrabajo $horario) {
            return [
                'id' => $horario->getId(),
                'colaborador_id' => $horario->getColaborador()->getId(),
                'fecha' => $horario->getHotFecha()->format('Y-m-d'),
                'hora_entrada' => $horario->getHotHoraentrada()->format('H:i'),
                'hora_salida' => $horario->getHotHorasalida()->format('H:i'),
                'tipo_jornada' => $horario->getHotTipojornada(),
                'descanso' => $horario->getHotDescanso(),
            ];
        }, $horarios);
    }
}
