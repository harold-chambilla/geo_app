<?php

namespace App\Controller\EasyControl\Colaborador\Asistencia;

use App\Entity\Asistencia;
use App\Repository\AsistenciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/resultado', name: 'app_easycontrol_colaborador_asistencia_resultado_')]
class ResultadoController extends AbstractController
{
    #[Route('/', name: 'verresultado')]
    public function verResultado(): Response
    {
        return $this->render('easy_control/colaborador/asistencia/resultado.html.twig');
    }

    #[Route('/api/asistencia', name: 'api_asistencia', methods: ['GET'])]
    public function asistencia(AsistenciaRepository $asistenciaRepository): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
        }

        $ultimoRegistro = $asistenciaRepository->findOneBy(['asi_colaborador' => $user], ['id' => 'DESC']);

        $entrada = $ultimoRegistro->getAsiHoraentrada();
        $salida = $ultimoRegistro->getAsiHorasalida();

        $empresa = $user->getColEmpresa();
        $confAsis = $empresa->getEmpConfiguracionesAsistencia();
        foreach ($confAsis as $conf) {
            # code...
            $modalidad = $conf->getCasModalidad();
        }
        // $conf_asistecia = $empresa->getCasEmpresa();

        $horariosTrabajo = $user->getColHorariostrabajo();
        // Iterar sobre los horarios de trabajo
        foreach ($horariosTrabajo as $horarioTrabajo) {
            // Acceder a la hora de entrada de cada horario de trabajo
            $horaEntrada = $horarioTrabajo->getHotHoraentrada();
            $horaSalida = $horarioTrabajo->getHotHorasalida();
            // Hacer algo con la hora de entrada...
        }
        $responseEntrada = [
            'usuario' => $user->getColNombres() . ' ' . $user->getColApellidos() ?? null,
            'dni' => $user->getColDninit(),
            'horaEntrada' => $horaEntrada->format('H:i:s') ?? null,
            'fecha' => $ultimoRegistro->getAsiFechaentrada()->format('d/m/Y') ?? null,
            'hora' => $ultimoRegistro->getAsiHoraentrada()->format('H:i:s') ?? null,
            'modalidad' => $modalidad ?? null,
            'estado' => $ultimoRegistro->getAsiEstadoentrada() ?? null,
            // 'ubicacion' => $ultimoRegistro->getAsiUbicacionentrada(),      
        ];

        if($ultimoRegistro->getAsiHorasalida())
        {
            $responseSalida = [
                'usuario' => $user->getColNombres() . ' ' . $user->getColApellidos() ?? null,
                'dni' => $user->getColDninit() ?? null,
                'horaEntrada' => $horaSalida->format('H:i:s') ?? null,
                'fecha' => $ultimoRegistro->getAsiFechasalida()->format('d/m/Y') ?? null,
                'hora' => $ultimoRegistro->getAsiHorasalida()->format('H:i:s') ?? null,    
                'modalidad' => $modalidad ?? null,    
                'estado' => $ultimoRegistro->getAsiEstadosalida() ?? null,
                // 'ubicacion' => $ultimoRegistro->getAsiUbicacionsalida(),
            ];
        }
        // Devuelve los datos como un JSON
        return $this->json([
            'entrada' => $responseEntrada,
            'salida' => $responseSalida ?? null,
        ]);
    }
}