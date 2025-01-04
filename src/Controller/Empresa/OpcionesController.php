<?php

namespace App\Controller\Empresa;

use App\Entity\Sede;
use App\Function\Empresa\AreaFunction;
use App\Function\Empresa\ConfiguracionAsistenciaFunction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Function\Empresa\EmpresaFunction;
use App\Function\Empresa\MotivoFunction;
use App\Function\Empresa\PuestoFunction;
use App\Function\Empresa\SedeFunction;
use App\Repository\SedeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/empresa/opciones', name: 'app_empresa_opciones_')]
class OpcionesController extends AbstractController
{
    public function __construct(
        private EmpresaFunction $empresaFunction,
        private AreaFunction $areaFunction,
        private PuestoFunction $puestoFunction,
        private MotivoFunction $motivoFunction,
        private SedeFunction $sedeFunction,
        private ConfiguracionAsistenciaFunction $configuracionAsistenciaFunction
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
    public function guardarSede(Request $request): JsonResponse
    {
        // $usuario = $this->getUser();
        // if (!$usuario) {
        //     return new JsonResponse(['error' => 'Usuario no autenticado'], JsonResponse::HTTP_UNAUTHORIZED);
        // }
    
        $empresaId = $request->request->get('empresaId');
        $nombre = $request->request->get('sed_nombre');
        $pais = $request->request->get('sed_pais');
        $direccion = $request->request->get('sed_direccion');
        $latitud = $request->request->get('latitud');
        $longitud = $request->request->get('longitud');

        $sedeData = [
            'sed_nombre' => $nombre,
            'sed_pais' => $pais,
            'sed_direccion' => $direccion,
            'sed_ubicacion' => [$latitud, $longitud]
        ];
    
        if (!$nombre || !$pais || !$direccion || !$latitud || !$longitud) {
            return new JsonResponse(['error' => 'Datos insuficientes.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $sede = $this->sedeFunction->registrarSede($empresaId, $sedeData);
        
        /*         
        $sede = new Sede();
        $sede->setSedNombre($nombre);
        $sede->setSedPais($pais);
        $sede->setSedDireccion($direccion);
        $sede->setSedUbicacion([$latitud, $longitud]); // Guardar como array [latitud, longitud]
        $sede->setSedEliminado(0);
    
        $entityManager->persist($sede);
        $entityManager->flush(); 
        */
    
        return $this->json([
            'success' => 'Sede creada con éxito',
            'sede' => $sede
        ], JsonResponse::HTTP_CREATED);
    }

    // API para crear un área
    #[Route('/api/crear-area/{empresaId}', name: 'crear_area', methods: ['POST'])]
    public function crearArea(int $empresaId, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $area = $this->areaFunction->registrarArea($empresaId, $data);
            return $this->json(['status' => 'success', 'data' => $area], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/listar/sedes', name: 'api_sedes_list', methods: ['GET'])]
    public function listarSedes(SedeRepository $sedeRepository): JsonResponse
    {
        // Obtener sedes que no estén eliminadas
        $sedes = $sedeRepository->findBy(['sed_eliminado' => false]);

        $dataSedes = [];
        foreach ($sedes as $sede) {
            $dataSedes[] = [
                'id' => $sede->getId(),
                'sed_nombre' => $sede->getSedNombre(),
                'sed_pais' => $sede->getSedPais(),
                'sed_direccion' => $sede->getSedDireccion(), 
                'sed_ubicacion' => $sede->getSedUbicacion()
            ];
        }     

        return $this->json([
            // 'success' => 'Estado de eliminación cambiado',
            'sede' => $dataSedes
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/api/sedes/{id}/eliminar', name: 'api_sedes_toggle_eliminado', methods: ['PATCH'])]
    public function toggleEliminado(int $id): JsonResponse
    {
    /*         
        $sede = $sedeRepository->find($id);

        if (!$sede) {
            return new JsonResponse(['error' => 'Sede no encontrada'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Cambiar el estado de sed_eliminado
        $sede->setSedEliminado(!$sede->isSedEliminado());
        $entityManager->persist($sede);
        $entityManager->flush(); 

        return $this->json([
            'success' => 'Estado de eliminación cambiado',
            'sede' => [
                'id' => $sede->getId(),
                'sed_nombre' => $sede->getSedNombre(),
                'sed_pais' => $sede->getSedPais(),
                'sed_direccion' => $sede->getSedDireccion(), 
                'sed_ubicacion' => $sede->getSedUbicacion(),
                'sed_eliminado' => $sede->isSedEliminado() 
            ]  
        ], JsonResponse::HTTP_OK);
    */


        $this->sedeFunction->borrarSede($id);

        $resultado = $this->sedeFunction->borrarSede($id);

        return $this->json([
            'status' => 'success',
            'sede' => $resultado
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/api/obtener-sedes/{empresaId}', name: 'obtener_sedes', methods: ['GET'])]
    public function obtenerSedes(int $empresaId): JsonResponse
    {
        try {
            // Llamar a la función para obtener las sedes
            $sedes = $this->sedeFunction->obtenerSedes($empresaId);

            return $this->json([
                'status' => 'success',
                'data' => $sedes,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    // API para obtener todas las áreas de una empresa
    #[Route('/api/obtener-areas/{empresaId}', name: 'obtener_areas', methods: ['GET'])]
    public function obtenerAreas(int $empresaId): JsonResponse
    {
        try {
            $areas = $this->areaFunction->obtenerAreas($empresaId);
            return $this->json(['status' => 'success', 'data' => $areas]);
        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    // API para obtener un área por su ID
    #[Route('/api/obtener-area/{areaId}', name: 'obtener_area', methods: ['GET'])]
    public function obtenerArea(int $areaId): JsonResponse
    {
        try {
            $area = $this->areaFunction->obtenerAreaPorId($areaId);
            return $this->json(['status' => 'success', 'data' => $area]);
        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    // API para eliminar un área
    #[Route('/api/eliminar-area/{areaId}', name: 'eliminar_area', methods: ['DELETE'])]
    public function eliminarArea(int $areaId): JsonResponse
    {
        try {
            $this->areaFunction->borrarArea($areaId);
            return $this->json(['status' => 'success', 'message' => 'Área eliminada exitosamente']);
        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    // API para crear un puesto en un área
    #[Route('/api/crear-puesto/{areaId}', name: 'crear_puesto', methods: ['POST'])]
    public function crearPuesto(int $areaId, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $puesto = $this->puestoFunction->registrarPuesto($areaId, $data);
            return $this->json(['status' => 'success', 'data' => $puesto], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    // API para obtener un puesto por su ID
    #[Route('/api/obtener-puesto/{puestoId}', name: 'obtener_puesto', methods: ['GET'])]
    public function obtenerPuesto(int $puestoId): JsonResponse
    {
        try {
            $puesto = $this->puestoFunction->obtenerPuestoPorId($puestoId);
            return $this->json(['status' => 'success', 'data' => $puesto]);
        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    // API para eliminar un puesto
    #[Route('/api/eliminar-puesto/{puestoId}', name: 'eliminar_puesto', methods: ['DELETE'])]
    public function eliminarPuesto(int $puestoId): JsonResponse
    {
        try {
            $this->puestoFunction->borrarPuesto($puestoId);
            return $this->json(['status' => 'success', 'message' => 'Puesto eliminado exitosamente']);
        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/registrar-motivo/{empresaId}', name: 'registrar_motivo', methods: ['POST'])]
    public function registrarMotivo(int $empresaId, Request $request): JsonResponse
    {
        try {
            $motivoData = json_decode($request->getContent(), true);
            $motivo = $this->motivoFunction->registrarMotivo($empresaId, $motivoData);

            return $this->json([
                'status' => 'success',
                'data' => $motivo,
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/obtener-motivos/{empresaId}', name: 'obtener_motivos', methods: ['GET'])]
    public function obtenerMotivos(int $empresaId): JsonResponse
    {
        try {
            $motivos = $this->motivoFunction->obtenerMotivos($empresaId);
            return $this->json([
                'status' => 'success',
                'data' => $motivos,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/eliminar-motivo/{motivoId}', name: 'eliminar_motivo', methods: ['DELETE'])]
    public function eliminarMotivo(int $motivoId): JsonResponse
    {
        try {
            $this->motivoFunction->borrarMotivo($motivoId);
            return $this->json([
                'status' => 'success',
                'message' => 'Motivo eliminado exitosamente',
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    // Nueva API para obtener configuración de asistencia "sistema" de una empresa
    #[Route('/api/obtener-configuracion-sistema/{empresaId}', name: 'obtener_configuracion_sistema', methods: ['GET'])]
    public function obtenerConfiguracionSistema(int $empresaId): JsonResponse
    {
        try {
            $configuracion = $this->configuracionAsistenciaFunction->obtenerConfiguracionAsistencia(null, $empresaId);
            return $this->json([
                'status' => 'success',
                'data' => $configuracion,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    // API para modificar configuración de asistencia "sistema" de una empresa o configuraciones vinculadas a un área
    #[Route('/api/editar-configuracion-sistema', name: 'editar_configuracion_sistema', methods: ['PUT'])]
    public function editarConfiguracionSistema(Request $request): JsonResponse
    {
        try {
            $nuevosDatos = json_decode($request->getContent(), true);
            $areaId = $nuevosDatos['area_id'] ?? null;
            $empresaId = $nuevosDatos['empresa_id'] ?? null;

            // Validar que uno de los dos IDs esté presente, pero no ambos
            if ($areaId && $empresaId) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'No se puede enviar ambos: area_id y empresa_id. Use solo uno.',
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            if (!$areaId && !$empresaId) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Debe proporcionar area_id o empresa_id para la operación.',
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Llamar a la función para editar la configuración, especificando el área o la empresa
            $configuracionActualizada = $this->configuracionAsistenciaFunction->editarConfiguracionAsistencia(
                $nuevosDatos,
                $empresaId,
                null,
                $areaId
            );

            return $this->json([
                'status' => 'success',
                'data' => $configuracionActualizada,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
