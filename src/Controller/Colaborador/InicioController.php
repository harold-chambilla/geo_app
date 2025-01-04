<?php

namespace App\Controller\Colaborador;

use App\Function\Colaborador\HorarioTrabajoFunction;
use App\Function\Colaborador\SedeFunction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/inicio', name: 'app_colaborador_inicio_')]
class InicioController extends AbstractController
{
    public function __construct(
        private SedeFunction $sedeFunction,
        private HorarioTrabajoFunction $horarioTrabajoFunction
    ){}
    
    #[Route(name: 'mostrar')]
    public function index(): Response
    {
        return $this->render('colaborador/inicio.html.twig');
    }

    #[Route('/api/sede', name: 'obtener_sede', methods: ['POST'])]
    public function obtenerSedePorColaborador(Request $request): JsonResponse
    {
        try {
            // 1. Obtener los datos del request
            $data = json_decode($request->getContent(), true);
            $colaboradorId = $data['colaborador_id'] ?? null;

            // 2. Validar que se recibió el ID del colaborador
            if (!$colaboradorId) {
                throw new \Exception('El ID del colaborador es obligatorio');
            }

            // 3. Llamar a la función que obtiene la sede
            $sede = $this->sedeFunction->obtenerSedePorColaborador($colaboradorId);

            // 4. Retornar la respuesta en formato JSON
            return $this->json([
                'status' => 'success',
                'data' => $sede,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/horario-colaborador', name: 'obtener_horario_colaborador', methods: ['POST'])]
    public function obtenerHorarioColaborador(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $colaboradorId = $data['colaborador_id'] ?? null;
            $fecha = isset($data['fecha']) ? new \DateTime($data['fecha']) : new \DateTime(); // Usa la fecha actual si no se envía

            if (!$colaboradorId) {
                throw new \Exception('El ID del colaborador es obligatorio');
            }

            $horario = $this->horarioTrabajoFunction->obtenerHorarioPorColaborador($colaboradorId, $fecha);

            return $this->json([
                'status' => 'success',
                'data' => $horario,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
