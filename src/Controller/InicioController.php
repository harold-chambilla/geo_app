<?php

namespace App\Controller;

use App\Entity\Asistencia;
use App\Repository\AsistenciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InicioController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/inicio', name: 'app_inicio')]
    public function index(): Response
    {
        return $this->render('inicio/index.html.twig', [
            'controller_name' => 'InicioController',
        ]);
    }

    #[Route('/api/asistencia', name: 'api_asistencia_create', methods: ['POST'])]
    public function create(Request $request, AsistenciaRepository $asistenciaRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->getUser();

        $existingEntry = $asistenciaRepository->findOneBy([
            'asi_colaborador' => $user,
            'asi_fechaentrada' => new \DateTime($data['asi_fechaentrada']),
            'asi_estadosalida' => null, // Buscar una entrada sin salida
        ]);
    
        // Si se encuentra una entrada sin salida, retornar un error
        if ($existingEntry) {
            return new JsonResponse(['message' => 'Ya existe una entrada sin salida para este colaborador en esta fecha'], JsonResponse::HTTP_BAD_REQUEST);
        }
   
        // Crear una nueva instancia de la entidad Asistencia
        $asistencia = new Asistencia();
        $asistencia->setAsiFechaentrada(new \DateTime($data['asi_fechaentrada']));
        $asistencia->setAsiHoraentrada(new \DateTime($data['asi_horaentrada']));
        $asistencia->setAsiFotoentrada($data['asi_fotoentrada']);
        $asistencia->setAsiEstadoentrada($data['asi_estadoentrada']);
        $asistencia->setAsiUbicacionentrada([$data['latitud'], $data['longitud']]);
        $asistencia->setAsiColaborador($user);
        $asistencia->setAsiEliminado(false);


        $this->entityManager->persist($asistencia);
        $this->entityManager->flush();

        return $this->json($asistencia, Response::HTTP_CREATED);
    }

    #[Route('/api/asistencia/salida', name: 'api_asistencia_update', methods: ['POST'])]
    public function update(Request $request, AsistenciaRepository $asistenciaRepository): JsonResponse
    {
        // Actualizar los datos de la asistencia
        $data = json_decode($request->getContent(), true);

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
        $asistencia->setAsiFechasalida(new \DateTime($data['asi_fechasalida']));
        $asistencia->setAsiHorasalida(new \DateTime($data['asi_horasalida']));
        $asistencia->setAsiFotosalida($data['asi_fotosalida']);
        $asistencia->setAsiEstadosalida($data['asi_estadosalida']);
        $asistencia->setAsiUbicacionsalida([$data['latitud'], $data['longitud']]);

        $this->entityManager->flush();

        return $this->json($asistencia, Response::HTTP_OK);
    }


    #[Route('/api/asistencia/prueba', name: 'api_asistencia_update_prueba', methods: ['GET'])]
    public function prueba(Request $request, AsistenciaRepository $asistenciaRepository): JsonResponse
    {
        // Obtener el usuario actual
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
    $response = [
        'id' => $lastAsistenciaId
    ];

    return $this->json($response, Response::HTTP_OK);
    }

    #[Route('/api/asistencia/{id}', name: 'api_asistencia_get', methods: ['GET'])]
    public function get(Asistencia $asistencia, AsistenciaRepository $asistenciaRepository): JsonResponse
    {
        if (!$asistencia) {
            return new JsonResponse(['message' => 'No se encontró la asistencia'], JsonResponse::HTTP_NOT_FOUND);
        }
        // $data = $asistenciaRepository->find($asistencia->getId());
        // $asis = [
        //     'asi_fechaentrada' => $data->getAsiFechaentrada()->format('Y-m-d'),
        //     'asi_horaentrada' => $data->getAsiHoraentrada()->format('H:i:s'),
        //     'asi_estadoentrada' => $data->getAsiEstadoentrada(),
        // ];

        $usuario = 1;
        $asistencias = $asistenciaRepository->findBy(['asi_colaborador' => $usuario, 'id' => $asistencia->getId()]);

        // Prepara la respuesta
        $response = [];
        foreach ($asistencias as $asistencia) {
            $response[] = [
                'asi_fechaentrada' => $asistencia->getAsiFechaentrada()->format('Y-m-d'),
                'asi_horaentrada' => $asistencia->getAsiHoraentrada()->format('H:i:s'),
                'asi_estadoentrada' => $asistencia->getAsiEstadoentrada(),
            ];
        }

        return $this->json($response, Response::HTTP_OK);
    }
}
