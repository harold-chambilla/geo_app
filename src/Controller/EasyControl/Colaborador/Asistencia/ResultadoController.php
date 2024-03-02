<?php

namespace App\Controller\EasyControl\Colaborador\Asistencia;

use App\Entity\Asistencia;
use App\Repository\AsistenciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/resultado', name: 'app_easycontrol_colaborador_asistencia_resultado_')]
class ResultadoController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'verresultado')]
    public function verResultado(): Response
    {
        return $this->render('easy_control/colaborador/asistencia/resultado.html.twig');
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