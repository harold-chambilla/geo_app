<?php

namespace App\Controller\Colaborador\Asistencia;

use App\Entity\Asistencia;
use App\Repository\AsistenciaRepository;
use App\Repository\ColaboradorRepository;
use App\Repository\SedeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/marcado', name: 'app_colaborador_asistencia_marcado_')]
class MarcadoController extends AbstractController
{
    private $entityManager;
    private $sedeRepository;

    public function __construct(EntityManagerInterface $entityManager, SedeRepository $sedeRepository){
        $this->entityManager = $entityManager;
        $this->sedeRepository = $sedeRepository;
    }

    #[Route('/', name: 'marcarasistencia')]
    public function marcarAsistencia(ColaboradorRepository $colaboradorRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $usuario_sec = $this->getUser()->getUserIdentifier();
        $usuario = $colaboradorRepository->findOneBy(
            [
                "col_nombreusuario" => $usuario_sec
            ]
        );
        $empresa = $usuario->getColEmpresa();
        return $this->render('colaborador/asistencia/marcado.html.twig', [
            'empresa' => $empresa->getEmpNombreempresa()
        ]);
    }
    
    #[Route('/api/asistencia/entrada', name: 'api_asistencia_entrada', methods: ['POST'])]
    public function entrada(Request $request, AsistenciaRepository $asistenciaRepository): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
        }

        $existingEntry = $asistenciaRepository->findOneBy([
            'asi_colaborador' => $user,
            'asi_fechaentrada' => new \DateTime($request->request->get('asi_fechaentrada')),
            'asi_estadosalida' => null, // Buscar una entrada sin salida
        ]);
          
        // Si se encuentra una entrada sin salida, retornar un error
        if ($existingEntry) {
            return new JsonResponse(['message' => 'Ya existe una entrada sin salida para este colaborador en esta fecha'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $archivo = $request->files->get('asi_fotoentrada');
        if ($archivo !== null) {
            // Si se ha adjuntado un archivo, moverlo al destino deseado
            $destino = $this->getParameter('kernel.project_dir') . '/public/img';
            $archivo->move($destino, $archivo->getClientOriginalName());
            $nombreArchivo = $archivo->getClientOriginalName();
        } else {
            // Si no se ha adjuntado un archivo, establecer el nombre de archivo como nulo o un valor predeterminado
            $nombreArchivo = null; // O establecer un nombre de archivo predeterminado
        }

        $asistencia = new Asistencia();
        $asistencia->setAsiFechaentrada(new \DateTime($request->request->get('asi_fechaentrada')));
        $asistencia->setAsiHoraentrada(new \DateTime($request->request->get('asi_horaentrada')));
        $asistencia->setAsiFotoentrada($nombreArchivo);
        $asistencia->setAsiEstadoentrada($request->request->get('asi_estadoentrada'));
        $asistencia->setAsiUbicacionentrada([$request->request->get('latitud'), $request->request->get('longitud')]);
        $asistencia->setAsiColaborador($user);
        $asistencia->setAsiEliminado(false);

        $this->entityManager->persist($asistencia);
        $this->entityManager->flush();

        return $this->json($asistencia, Response::HTTP_CREATED);
    }

    #[Route('/api/asistencia/salida', name: 'api_asistencia_salida', methods: ['POST'])]
    public function salida(Request $request, AsistenciaRepository $asistenciaRepository): JsonResponse
    {
        $user = $this->getUser();
        // Verificar si el usuario actual está autenticado
        if (!$user) {
            return new JsonResponse(['message' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
        }
        // Obtener el último registro de asistencia del usuario
        $lastAsistencia = $asistenciaRepository->findLastAsistenciaByUser($user);
        // Verificar si se encontró algún registro de asistencia para el usuario
        if (!$lastAsistencia) {
            return new JsonResponse(['message' => 'No se encontraron registros de asistencia para este usuario'], Response::HTTP_NOT_FOUND);
        }
        // Obtener el ID del último registro de asistencia del usuario
        $lastAsistenciaId = $lastAsistencia->getId();
        // Preparar la respuesta
        $responseId = [
            'id' => $lastAsistenciaId
        ];

        $asistencia = $asistenciaRepository->find($responseId);
        if (!$asistencia) {
            return new JsonResponse(['message' => 'No se encontró la asistencia'], JsonResponse::HTTP_NOT_FOUND);
        }

        $archivo = $request->files->get('asi_fotoentrada');
        if ($archivo !== null) {
            // Si se ha adjuntado un archivo, moverlo al destino deseado
            $destino = $this->getParameter('kernel.project_dir') . '/public/img';
            $archivo->move($destino, $archivo->getClientOriginalName());
            $nombreArchivo = $archivo->getClientOriginalName();
        } else {
            // Si no se ha adjuntado un archivo, establecer el nombre de archivo como nulo o un valor predeterminado
            $nombreArchivo = null; // O establecer un nombre de archivo predeterminado
        }

        $asistencia->setAsiFechasalida(new \DateTime($request->request->get('asi_fechasalida')));
        $asistencia->setAsiHorasalida(new \DateTime($request->request->get('asi_horasalida')));
        $asistencia->setAsiFotosalida($nombreArchivo);
        $asistencia->setAsiEstadosalida($request->request->get('asi_estadosalida'));
        $asistencia->setAsiUbicacionsalida([$request->request->get('latitud'), $request->request->get('longitud')]);

        $this->entityManager->flush();

        return $this->json($asistencia, Response::HTTP_OK);
    }

    #[Route('/api/listar/sedes', name: 'listar_sedes_marcado', methods: ['GET'])]
    public function listarSedesMarcado(): JsonResponse
    {
        // Obtener el usuario autenticado
        $usuario = $this->getUser();

        if (!$usuario) {
            return new JsonResponse(['error' => 'Colaborador no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Obtener la empresa a la que pertenece el usuario
        $empresa = $usuario->getColEmpresa(); // Suponiendo que el usuario tiene una relación con la empresa

        if (!$empresa) {
            return new JsonResponse(['error' => 'Empresa no encontrada'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Obtener las sedes de la empresa
        $sedes = $this->sedeRepository->findBy(['sed_empresa' => $empresa]);

        if (empty($sedes)) {
            return new JsonResponse(['error' => 'No se encontraron sedes para esta empresa'], JsonResponse::HTTP_NOT_FOUND);
        }
        
        // Radio por defecto para las sedes
        $radioPorDefecto = 300; // Valor en metros

        // Crear una respuesta con la información de las sedes
        $data = [];
        foreach ($sedes as $sede) {
            $data[] = [
                'id' => $sede->getId(),
                'nombre' => $sede->getSedNombre(),
                'direccion' => $sede->getSedDireccion(),
                'latitud' => $sede->getSedUbicacion()[0],
                'longitud' => $sede->getSedUbicacion()[1],
                'radio' => $radioPorDefecto
            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }
}
