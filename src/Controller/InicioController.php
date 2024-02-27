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
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Crear una nueva instancia de la entidad Asistencia
        $asistencia = new Asistencia();
        $asistencia->setAsiFechaentrada(new \DateTime($data['asi_fechaentrada']));
        // $asistencia->setAsiFechasalida(new \DateTime($data['asi_fechasalida']));
        $asistencia->setAsiHoraentrada(new \DateTime($data['asi_horaentrada']));
        // $asistencia->setAsiHorasalida(new \DateTime($data['asi_horasalida']));
        $asistencia->setAsiFotoentrada($data['asi_fotoentrada']);
        // $asistencia->setAsiFotosalida($data['asi_fotosalida']);
        $asistencia->setAsiEstadoentrada($data['asi_estadoentrada']);
        // $asistencia->setAsiEstadosalida($data['asi_estadosalida']);
        $asistencia->setAsiUbicacion([$data['latitud'], $data['longitud']]);
        $asistencia->setAsiEliminado(false);


        $this->entityManager->persist($asistencia);
        $this->entityManager->flush();

        return $this->json($asistencia, Response::HTTP_CREATED);
    }

    #[Route('/api/asistencia/{id}', name: 'api_asistencia_update', methods: ['PUT'])]
    public function update(Asistencia $asistencia, Request $request): JsonResponse
    {
        // Actualizar los datos de la asistencia
        $data = json_decode($request->getContent(), true);

        if (!$asistencia) {
            return new JsonResponse(['message' => 'No se encontró la asistencia'], JsonResponse::HTTP_NOT_FOUND);
        }

        $asistencia->setAsiFechasalida(new \DateTime($data['asi_fechasalida']));
        $asistencia->setAsiHorasalida(new \DateTime($data['asi_horasalida']));
        $asistencia->setAsiFotosalida($data['asi_fotosalida']);
        $asistencia->setAsiEstadosalida($data['asi_estadosalida']);

        $this->entityManager->flush();

        return $this->json($asistencia, Response::HTTP_OK);
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
