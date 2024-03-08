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

    #[Route('/api/asistencia/entrada/prueba', name: 'api_asistencia_create', methods: ['POST'])]
    public function create(Request $request, AsistenciaRepository $asistenciaRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->getUser();

        $existingEntry = $asistenciaRepository->findOneBy([
            'asi_colaborador' => $user,
            'asi_fechaentrada' => new \DateTime($request->request->get('asi_fechaentrada')),
            'asi_estadosalida' => null, // Buscar una entrada sin salida
        ]);
    
        if (!$user) {
            return new JsonResponse(['message' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
        }

        // Si se encuentra una entrada sin salida, retornar un error
        if ($existingEntry) {
            return new JsonResponse(['message' => 'Ya existe una entrada sin salida para este colaborador en esta fecha'], JsonResponse::HTTP_BAD_REQUEST);
        }
   
        // Imagen
        // $archivo = $data['asi_fotoentrada'];
        $archivo = $request->files->get('asi_fotoentrada');
        // $destino = $this->getParameter('kernel.project_dir').'/public/img';
        // $archivo->move($destino, $archivo->getClientOriginalName());

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

         // Decodificar el archivo de Base64 a un archivo temporal
    // $decodedFile = base64_decode($data['asi_fotoentrada']);
    // $tempFilePath = tempnam(sys_get_temp_dir(), 'photo');
    // file_put_contents($tempFilePath, $decodedFile);

    // // Mover el archivo temporal al destino deseado
    // $destinationPath = $this->getParameter('kernel.project_dir') . '/public/img';
    // $fileName = 'new_filename.jpg'; // Nombre de archivo deseado
    // $destinationFilePath = $destinationPath . '/' . $fileName;
    // rename($tempFilePath, $destinationFilePath);

        // Crear una nueva instancia de la entidad Asistencia
        // $asistencia = new Asistencia();
        // $asistencia->setAsiFechaentrada(new \DateTime($data['asi_fechaentrada']));
        // $asistencia->setAsiHoraentrada(new \DateTime($data['asi_horaentrada']));
        // $asistencia->setAsiFotoentrada($archivo->getClientOriginalName());
        // $asistencia->setAsiEstadoentrada($data['asi_estadoentrada']);
        // $asistencia->setAsiUbicacionentrada([$data['latitud'], $data['longitud']]);
        // $asistencia->setAsiColaborador($user);
        // $asistencia->setAsiEliminado(false);


        $this->entityManager->persist($asistencia);
        $this->entityManager->flush();

        return $this->json($asistencia, Response::HTTP_CREATED);
    }

    #[Route('/api/asistencia/salida/prueba', name: 'api_asistencia_update', methods: ['POST'])]
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

        // $asistencia->setAsiFechasalida(new \DateTime($data['asi_fechasalida']));
        // $asistencia->setAsiHorasalida(new \DateTime($data['asi_horasalida']));
        // $asistencia->setAsiFotosalida($data['asi_fotosalida']);
        // $asistencia->setAsiEstadosalida($data['asi_estadosalida']);
        // $asistencia->setAsiUbicacionsalida([$data['latitud'], $data['longitud']]);

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
