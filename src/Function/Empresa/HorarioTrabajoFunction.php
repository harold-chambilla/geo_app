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
        $tipo_registro = $data['tipo_registro'] ?? 'individual';
        $colaborador_ids = $data['colaborador_ids'] ?? [];
        $dia_inicio = $data['dia_inicio'] ?? null;
        $dia_fin = $data['dia_fin'] ?? null;
        $fechas = $data['fechas'] ?? [];
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
            'dia' => $this->registrarPorDia($colaborador_ids, $hora_entrada, $hora_salida, $fechas, $tipo_jornada, $descanso),
            'colaborador' => $this->registrarPorColaborador($colaborador_ids, $fechas, $hora_entrada, $hora_salida, $descanso, $tipo_jornada, $aplicar_a_todo),
            default => [],
        };
    }

    private function normalizarDiaDescanso(string $descanso): string
    {
        $dias_semana = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
            'Lunes' => 'Lunes',
            'Martes' => 'Martes',
            'Miércoles' => 'Miércoles',
            'Jueves' => 'Jueves',
            'Viernes' => 'Viernes',
            'Sábado' => 'Sábado',
            'Domingo' => 'Domingo',
        ];
    
        return $dias_semana[$descanso] ?? ucfirst(strtolower($descanso));
    }
    
    private function obtenerDiasDeDescanso(\DateTime $fecha_inicio, \DateTime $fecha_fin, string $descanso): array
    {
        $dias_descanso = [];
        $fecha_temp = clone $fecha_inicio;
    
        while ($fecha_temp <= $fecha_fin) {
            $dia_actual = $this->normalizarDiaDescanso($fecha_temp->format('l'));
            if ($dia_actual === $descanso) {
                $dias_descanso[] = $fecha_temp->format('Y-m-d');
            }
            $fecha_temp->modify('+1 day');
        }
    
        return $dias_descanso;
    }
    
    private function registrarPorMes(array $colaborador_ids, string $hora_entrada, string $hora_salida, string $dia_inicio, string $dia_fin, string $descanso, string $tipo_jornada): array
    {
        $colaboradores = $this->obtenerColaboradores($colaborador_ids);
        $fecha_inicio = new \DateTime($dia_inicio);
        $fecha_fin = new \DateTime($dia_fin);
        $registros = [];
        $descanso_normalizado = $this->normalizarDiaDescanso($descanso);
        $dias_descanso = $this->obtenerDiasDeDescanso($fecha_inicio, $fecha_fin, $descanso_normalizado);
    
        while ($fecha_inicio <= $fecha_fin) {
            if (!in_array($fecha_inicio->format('Y-m-d'), $dias_descanso)) {
                foreach ($colaboradores as $colaborador) {
                    if (!$this->existeHorario($colaborador, $fecha_inicio)) {
                        $registros[] = $this->crearHorario(
                            $colaborador,
                            clone $fecha_inicio,
                            $hora_entrada,
                            $hora_salida,
                            $descanso_normalizado,
                            $tipo_jornada
                        );
                    }
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
        $descanso_normalizado = $this->normalizarDiaDescanso($descanso);
        $dias_descanso = $this->obtenerDiasDeDescanso($fecha_inicio, $fecha_fin, $descanso_normalizado);
    
        while ($fecha_inicio <= $fecha_fin) {
            if (!in_array($fecha_inicio->format('Y-m-d'), $dias_descanso)) {
                foreach ($colaboradores as $colaborador) {
                    if (!$this->existeHorario($colaborador, $fecha_inicio)) {
                        $registros[] = $this->crearHorario(
                            $colaborador,
                            clone $fecha_inicio,
                            $hora_entrada,
                            $hora_salida,
                            $descanso_normalizado,
                            $tipo_jornada
                        );
                    }
                }
            }
            $fecha_inicio->modify('+1 day');
        }
    
        return $registros;
    }    

    private function registrarPorDia(array $colaborador_ids, string $hora_entrada, string $hora_salida, array $fechas, string $tipo_jornada, string $descanso): array
    {
        $colaboradores = $this->obtenerColaboradores($colaborador_ids);
        $registros = [];
    
        foreach ($fechas as $fecha) {
            $fecha_registro = new \DateTime($fecha);
    
            foreach ($colaboradores as $colaborador) {
                if (!$this->existeHorario($colaborador, $fecha_registro)) {
                    $registros[] = $this->crearHorario(
                        $colaborador,
                        $fecha_registro,
                        $hora_entrada,
                        $hora_salida,
                        $descanso, // Se incluye el descanso aunque no afecta la lógica
                        $tipo_jornada
                    );
                }
            }
        }
    
        return $registros;
    }    
    
    private function registrarPorColaborador(
        array $colaborador_ids, 
        array $fechas, 
        string $hora_entrada, 
        string $hora_salida, 
        string $descanso, 
        string $tipo_jornada, 
        bool $aplicar_a_todo
    ): array {
        $colaboradores = $this->obtenerColaboradores($colaborador_ids);
        $registros = [];
    
        if ($aplicar_a_todo) {
            foreach ($colaboradores as $colaborador) {
                foreach ($fechas as $rango) {
                    if (is_array($rango) && count($rango) === 2) {
                        // Caso: aplicar_a_todo con rangos de fechas (semana)
                        [$dia_inicio, $dia_fin] = $rango; // Desestructurar el rango
                        $registros = array_merge($registros, $this->registrarPorSemana(
                            [$colaborador->getId()],
                            $hora_entrada,
                            $hora_salida,
                            $dia_inicio,
                            $dia_fin,
                            $descanso,
                            $tipo_jornada
                        ));
                    } elseif (is_string($rango)) {
                        // Caso: aplicar_a_todo con fechas individuales
                        $registros = array_merge($registros, $this->registrarPorDia(
                            [$colaborador->getId()],
                            $hora_entrada,
                            $hora_salida,
                            [$rango],
                            $tipo_jornada,
                            $descanso
                        ));
                    } else {
                        throw new \InvalidArgumentException('Formato incorrecto en el array de fechas para aplicar_a_todo.');
                    }
                }
            }
        } else {
            // Caso: Sin aplicar_a_todo
            foreach ($colaboradores as $colaborador) {
                foreach ($fechas as $fecha) {
                    if (is_array($fecha) && count($fecha) === 2) {
                        // Registro por semana
                        [$dia_inicio, $dia_fin] = $fecha; // Desestructurar el rango
                        $registros = array_merge($registros, $this->registrarPorSemana(
                            [$colaborador->getId()],
                            $hora_entrada,
                            $hora_salida,
                            $dia_inicio,
                            $dia_fin,
                            $descanso,
                            $tipo_jornada
                        ));
                    } elseif (is_string($fecha)) {
                        // Registro por día
                        $registros = array_merge($registros, $this->registrarPorDia(
                            [$colaborador->getId()],
                            $hora_entrada,
                            $hora_salida,
                            [$fecha],
                            $tipo_jornada,
                            $descanso
                        ));
                    } else {
                        throw new \InvalidArgumentException('Formato incorrecto en el array de fechas.');
                    }
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