<?php

namespace App\Funciones\Empresa;

use App\Entity\HorarioTrabajo;
use Doctrine\ORM\EntityManagerInterface;
use PDOException;

class HorariosFunciones
{
    public function __construct(private EntityManagerInterface $entityManagerInterface){ }

    public function registro(array $horario): string
    {
        $estado = 'Ok';
        $aux_horario = new HorarioTrabajo;
        $aux_horario->setHotDiasemana($horario['hot_diasemana'] ?? null);
        $aux_horario->setHotDiasemana($horario['hot_fecha'] ?? null);
        $aux_horario->setHotDiasemana($horario['hot_horaentrada'] ?? null);
        $aux_horario->setHotDiasemana($horario['hot_horasalida'] ?? null);
        $aux_horario->setHotDiasemana($horario['hot_tipojornada'] ?? null);
        $aux_horario->setHotDiasemana($horario['hot_eliminado'] ?? null);
        $aux_horario->setHotColaborador($horario['hot_colaborador_id' ?? null]);

        try {
            $this->entityManagerInterface->persist($aux_horario);
            $this->entityManagerInterface->flush();
        } catch (PDOException $e) {
            $estado = 'Error: '. $e; 
        }

        return $estado;
    }

    public function registroMasivo(array $horarios): array
    {
        $estado = 'Ok';
        $errores = [];

        foreach ($horarios as $horario) {
           array_push($errores, $this->registro($horario));
        }

        if(count($errores) > 0){
            $estado = 'Errores encontrados: ' . count($errores);
        }

        return [
            'estado' => $estado,
            'errores' => $errores
        ];
    }
}