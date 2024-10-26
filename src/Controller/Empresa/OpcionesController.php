<?php

namespace App\Controller\Empresa;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Function\Empresa\EmpresaFunction;
use Symfony\Component\HttpFoundation\Request;

#[Route('/empresa/opciones', name: 'app_empresa_opciones_')]
class OpcionesController extends AbstractController
{
    public function __construct(
        private EmpresaFunction $empresaFunction
    ){}

    #[Route('/', name: 'inicio')]
    public function index(): Response
    {
        return $this->render('empresa/opciones.html.twig');
    }

    #[Route('/api/obtener-empresa/{empresaId}', name: 'obtener_empresa', methods: ['GET'])]
    public function obtenerEmpresa(int $empresaId): JsonResponse
    {
        try {
            // Llamamos a la función para obtener la empresa con sus relaciones
            $empresaData = $this->empresaFunction->obtenerEmpresaConRelaciones($empresaId);

            return $this->json([
                'status' => 'success',
                'data' => $empresaData,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
