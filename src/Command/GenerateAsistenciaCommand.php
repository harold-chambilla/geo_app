<?php

namespace App\Command;

use App\Controller\AjustesController;
use App\Entity\HorarioTrabajo;
use App\Entity\Asistencia;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-asistencias',
    description: 'Genera asistencias diarias en estado pendiente según los horarios registrados.',
)]
class GenerateAsistenciaCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager, private AjustesController $ajustesController)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // Obtener la fecha desde el método del controlador y decodificar el JSON
        $response = $this->ajustesController->getDateTime(new \Symfony\Component\HttpFoundation\Request());
        $data = json_decode($response->getContent(), true);
    
        if (!$data || !isset($data['date'])) {
            $io->error('No se pudo obtener la fecha desde el controlador.');
            return Command::FAILURE;
        }
    
        // Crear objeto DateTime desde la fecha obtenida y formatearlo correctamente
        $fechaHoy = \DateTime::createFromFormat('d-m-Y', $data['date']);
        if (!$fechaHoy) {
            $io->error('Error al parsear la fecha.');
            return Command::FAILURE;
        }

        // Asegurar que la fecha es comparable con la base de datos
        $fechaHoyFormatted = $fechaHoy->format('Y-m-d');
        $io->info('Procesando asistencias para el día: ' . $fechaHoyFormatted);
    
        // Obtener horarios de trabajo activos para hoy
        $horarios = $this->entityManager->getRepository(HorarioTrabajo::class)
            ->findBy(['hot_fecha' => $fechaHoy, 'hot_eliminado' => false]);
    
        if (empty($horarios)) {
            $io->warning('No hay horarios registrados para hoy.');
            return Command::SUCCESS;
        }
    
        foreach ($horarios as $horario) {
            $colaborador = $horario->getColaborador();
    
            // Verificar si ya existe un registro de asistencia para este colaborador y fecha
            $existeAsistencia = $this->entityManager->getRepository(Asistencia::class)
                ->findOneBy([
                    'colaborador' => $colaborador,
                    'asi_fechaentrada' => new \DateTime($fechaHoyFormatted) // Se usa DateTime para asegurar coincidencia con la BD
                ]);

            if ($existeAsistencia) {
                $io->comment('Ya existe una asistencia para ' . $colaborador->getId() . ', se omite.');
                continue;
            }

            // Crear nueva asistencia en estado "Pendiente"
            $asistencia = new Asistencia();
            $asistencia->setColaborador($colaborador);
            $asistencia->setAsiFechaentrada(new \DateTime($fechaHoyFormatted));
            $asistencia->setAsiEstadoentrada("pendiente");
            $asistencia->setAsiEstadosalida("pendiente");
            $asistencia->setAsiEliminado(false);
            $asistencia->setAsiNotas("Asistencia generada automáticamente.");
            
            $this->entityManager->persist($asistencia);
            $io->success('Asistencia creada para: ' . $colaborador->getId());
        }
    
        $this->entityManager->flush();
        $io->success('Asistencias generadas exitosamente.');
    
        return Command::SUCCESS;
    }    
}
