<?php

namespace App\Controller\Empresa;

use App\Entity\Sede;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Function\Empresa\EmpresaFunction;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/api/guardar/sede', name: 'guardar_sede', methods: ['POST'])]
    public function guardarSede(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario) {
            return new JsonResponse(['error' => 'Usuario no autenticado'], JsonResponse::HTTP_UNAUTHORIZED);
        }
    
        $empresaId = $request->request->get('empresaId');
        $nombre = $request->request->get('sed_nombre');
        $pais = $request->request->get('sed_pais');
        $direccion = $request->request->get('sed_direccion');
        $latitud = $request->request->get('latitud');
        $longitud = $request->request->get('longitud');
    
        if (!$nombre || !$pais || !$direccion || !$latitud || !$longitud) {
            return new JsonResponse(['error' => 'Datos insuficientes.'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        $sede = new Sede();
        $sede->setSedNombre($nombre);
        $sede->setSedPais($pais);
        $sede->setSedDireccion($direccion);
        // $sede->setSedUbicacion([$latitud, $longitud]); // Guardar como array [latitud, longitud]
    
        $entityManager->persist($sede);
        $entityManager->flush();
    
        return new JsonResponse([
            'sed_id' => $sede->getId(),
            'sed_nombre' => $sede->getSedNombre(),
            'sed_pais' => $sede->getSedPais(),
            'sed_direccion' => $sede->getSedDireccion(),
            'sed_ubicacion' => $sede->getSedUbicacion()
        ], JsonResponse::HTTP_CREATED);
    }
}
