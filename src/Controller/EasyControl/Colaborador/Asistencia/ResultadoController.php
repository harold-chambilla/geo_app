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

    #[Route('/api/asistencia/entrada', name: 'api_asistencia_ultima_entrada', methods: ['GET'])]
    public function resultadoEntrada(AsistenciaRepository $asistenciaRepository): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
        }

        $lastEntrada = $asistenciaRepository->findLastAsistenciaByUserEntrada($user);
        // Verificar si se encontró algún registro de asistencia para el usuario
        if (!$lastEntrada) {
            return new JsonResponse(['message' => 'No se encontraron registros de asistencia para este usuario'], Response::HTTP_NOT_FOUND);
        }
        // Obtener el ID del último registro de asistencia del usuario
        // Preparar la respuesta
        $response = [
            'asi_fechaentrada' => $lastEntrada->getAsiFechaentrada()->format('Y-m-d'),
            'asi_horaentrada' => $lastEntrada->getAsiHoraentrada()->format('H:i:s'),
            'asi_estadoentrada' => $lastEntrada->getAsiEstadoentrada(),
        ];
    
        return $this->json($response, Response::HTTP_OK);
    }

    #[Route('/api/asistencia/salida', name: 'api_asistencia_ultima_salida', methods: ['GET'])]
    public function resultadoSalida(AsistenciaRepository $asistenciaRepository): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
        }
        // Obtener el último registro de asistencia de salida del usuario
        $lastSalida = $asistenciaRepository->findLastAsistenciaByUserSalida($user);

        // Verificar si se encontró algún registro de salida para el usuario
        if (!$lastSalida) {
            return new JsonResponse(['message' => 'No se encontraron registros de salida para este usuario'], Response::HTTP_NOT_FOUND);
        }

        // Preparar la respuesta con los datos de salida
        $response = [
            'asi_fechasalida' => $lastSalida->getAsiFechasalida()->format('Y-m-d'),
            'asi_horasalida' => $lastSalida->getAsiHorasalida()->format('H:i:s'),
            'asi_estadosalida' => $lastSalida->getAsiEstadosalida(),
        ];

        return $this->json($response, Response::HTTP_OK);
    }

    #[Route('/api/asistencia/salida/prueba', name: 'api_asistencia_ultimas_salida', methods: ['GET'])]
    public function resultado(AsistenciaRepository $asistenciaRepository): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
        }
        // // Obtener el último registro de asistencia de salida del usuario
        // $lastSalida = $asistenciaRepository->findLastAsistenciaByUserSalida($user);

        // // Verificar si se encontró algún registro de salida para el usuario
        // if (!$lastSalida) {
        //     return new JsonResponse(['message' => 'No se encontraron registros de salida para este usuario'], Response::HTTP_NOT_FOUND);
        // }

        $ultimoRegistro = $asistenciaRepository->findOneBy(['asi_colaborador' => $user], ['id' => 'DESC']);

        $entrada = $ultimoRegistro->getAsiHoraentrada();
        $salida = $ultimoRegistro->getAsiHorasalida();

        $horariosTrabajo = $user->getColHorariostrabajo();
        // Iterar sobre los horarios de trabajo
        foreach ($horariosTrabajo as $horarioTrabajo) {
            // Acceder a la hora de entrada de cada horario de trabajo
            $horaEntrada = $horarioTrabajo->getHotHoraentrada();
            $horaSalida = $horarioTrabajo->getHotHorasalida();
            // Hacer algo con la hora de entrada...
        }
        $responseEntrada = [
            'usuario' => $user->getColNombres() . ' ' . $user->getColApellidos(),
            'dni' => $user->getColDninit(),
            'horaEntrada' => $horaEntrada->format('H:i:s'),
            'fecha' => $ultimoRegistro->getAsiFechaentrada()->format('d/m/Y'),
            'hora' => $ultimoRegistro->getAsiHoraentrada()->format('H:i:s'),
            'estado' => $ultimoRegistro->getAsiEstadoentrada(),
            'ubicacion' => $ultimoRegistro->getAsiUbicacionentrada(),
        ];

        if($ultimoRegistro->getAsiHorasalida())
        {
            $responseSalida = [
                'usuario' => $user->getColNombres() . ' ' . $user->getColApellidos(),
                'dni' => $user->getColDninit(),
                'horaEntrada' => $horaSalida->format('H:i:s'),
                'fecha' => $ultimoRegistro->getAsiFechasalida()->format('d/m/Y'),
                'hora' => $ultimoRegistro->getAsiHorasalida()->format('H:i:s'),        
                'estado' => $ultimoRegistro->getAsiEstadosalida(),
                'ubicacion' => $ultimoRegistro->getAsiUbicacionsalida(),
            ];
        }
        // Devuelve los datos como un JSON
        return $this->json([
            'entrada' => $responseEntrada,
            'salida' => $responseSalida ?? null,
        ]);
    }
}