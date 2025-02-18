<?php

namespace App\Controller\Empresa;

use App\Function\Empresa\AsistenciaFunction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/empresa/asistencia', name: 'app_empresa_asistencia_')]
class AsistenciaController extends AbstractController
{
    public function __construct(private AsistenciaFunction $asistenciaFunction) {}

    #[Route('/', name: 'inicio')]
    public function index(): Response
    {
        return $this->render('empresa/asistencia.html.twig');
    }

    #[Route('/api/obtener', name: 'obtener', methods: ['POST'])]
    public function listarAsistencias(Request $request): JsonResponse
    {
        return $this->asistenciaFunction->obtenerAsistencias($request);
    }
}