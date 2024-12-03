<?php

namespace App\Controller\Empresa;

use App\Function\Empresa\HorarioTrabajoFunction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/empresa/horario', name: 'app_empresa_horario_')]
class HorarioController extends AbstractController
{
    public function __construct(private HorarioTrabajoFunction $horarioTrabajoFunction) {}

    #[Route('/', name: 'inicio')]
    public function index(): Response
    {
        return $this->render('empresa/horario.html.twig');
    }

    #[Route('/api/registrar', name: 'registrar_horario', methods: ['POST'])]
    public function registrarHorario(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'el cuerpo de la solicitud está vacío.',
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $tipo_registro = $data['tipo_registro'] ?? null;
            if (!$tipo_registro || !in_array($tipo_registro, ['mes', 'semana', 'dia', 'colaborador'], true)) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'el tipo de registro es obligatorio y debe ser "mes", "semana", "dia" o "colaborador".',
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            if (!isset($data['colaborador_ids']) || !is_array($data['colaborador_ids']) || count($data['colaborador_ids']) === 0) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'debe enviarse un array con los ids de los colaboradores.',
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $required_fields = ['hora_entrada', 'hora_salida'];
            if (in_array($tipo_registro, ['mes', 'semana'], true)) {
                $required_fields = array_merge($required_fields, ['dia_inicio', 'dia_fin']);
            } elseif ($tipo_registro === 'dia') {
                $required_fields[] = 'fecha';
            }

            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    return $this->json([
                        'status' => 'error',
                        'message' => sprintf('el campo "%s" es obligatorio.', $field),
                    ], JsonResponse::HTTP_BAD_REQUEST);
                }
            }

            $data['tipo_jornada'] = $data['tipo_jornada'] ?? 'presencial';
            if (!in_array($data['tipo_jornada'], ['presencial', 'remota', 'descanso', 'no laborable'], true)) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'el tipo de jornada debe ser "presencial", "remota", "descanso" o "no laborable".',
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $data['descanso'] = $data['descanso'] ?? 'no establecido';
            $data['aplicar_a_todo'] = $data['aplicar_a_todo'] ?? false;

            $resultados = $this->horarioTrabajoFunction->registrarHorario($data);

            return $this->json([
                'status' => 'success',
                'data' => $resultados,
            ], JsonResponse::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'ocurrió un error inesperado: ' . $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/modificar/{horario_id}', name: 'modificar_horario', methods: ['PUT'])]
    public function modificarHorario(int $horario_id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'el cuerpo de la solicitud está vacío.',
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $resultado = $this->horarioTrabajoFunction->modificarHorario($horario_id, $data);

            return $this->json([
                'status' => 'success',
                'data' => $resultado,
            ], JsonResponse::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'ocurrió un error inesperado: ' . $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/eliminar/{horario_id}', name: 'eliminar_horario', methods: ['DELETE'])]
    public function eliminarHorario(int $horario_id): JsonResponse
    {
        try {
            $resultado = $this->horarioTrabajoFunction->eliminarHorarioLogico($horario_id);

            return $this->json([
                'status' => 'success',
                'message' => $resultado['message'],
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/obtener', name: 'obtener_horarios', methods: ['POST'])]
    public function obtenerHorarios(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['colaborador_ids']) || !is_array($data['colaborador_ids']) || count($data['colaborador_ids']) === 0) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'debe enviarse un array con los ids de los colaboradores.',
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $resultados = $this->horarioTrabajoFunction->obtenerHorarios($data['colaborador_ids']);

            return $this->json([
                'status' => 'success',
                'data' => $resultados,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
