<?php

namespace App\Controller\Colaborador;

use App\Function\Colaborador\AsistenciaFunction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/asistencia', name: 'app_asistencia_')]
class AsistenciaController extends AbstractController 
{
    public function __construct(private AsistenciaFunction $asistenciaFunction) {}

    #[Route('/api/crear', name: 'api_crear', methods: ['POST'])]
    public function crearAsistencia(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Datos inválidos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $response = $this->asistenciaFunction->crearAsistencia($data);

        return $this->json($response);
    }

    #[Route('/api/obtener', name: 'api_obtener', methods: ['GET'])]
    public function obtenerAsistencia(Request $request): JsonResponse
    {
        $criteria = $request->query->all();

        if (empty($criteria)) {
            return $this->json(['error' => 'Criterios de búsqueda no proporcionados'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $response = $this->asistenciaFunction->obtenerAsistencia($criteria);

        return $this->json($response);
    }

    #[Route('/api/actualizar/{id}', name: 'api_actualizar', methods: ['PUT'])]
    public function actualizarAsistencia(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Datos inválidos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $response = $this->asistenciaFunction->actualizarAsistencia($id, $data);

        return $this->json($response);
    }

    #[Route('/api/eliminar/{id}', name: 'eliminar', methods: ['DELETE'])]
    public function eliminarAsistencia(int $id): JsonResponse
    {
        $response = $this->asistenciaFunction->eliminarAsistencia($id);

        return $this->json($response);
    }
}