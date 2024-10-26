<?php

namespace App\Controller\Empresa;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Function\Empresa\EmpresaFunction;

#[Route('/empresa/arranque', name: 'app_empresa_arranque_')]
class ArranqueController extends AbstractController
{
    public function __construct(
        private EmpresaFunction $empresaFunction
    ){}

    // API para crear una nueva empresa
    #[Route('/api/crear-empresa', name: 'crear_empresa', methods: ['POST'])]
    public function crearEmpresa(Request $request): JsonResponse
    {
        try {
            // Decodificamos el contenido del JSON enviado en la solicitud
            $data = json_decode($request->getContent(), true);

            // Llamamos a la función para crear la empresa con los datos proporcionados
            $empresa = $this->empresaFunction->crearEmpresa($data);

            // Retornamos la respuesta como un array
            return $this->json([
                'status' => 'success',
                'data' => $empresa
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}


