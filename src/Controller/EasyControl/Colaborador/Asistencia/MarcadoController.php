<?php

namespace App\Controller\EasyControl\Colaborador\Asistencia;

use App\Entity\Asistencia;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/marcado', name: 'app_easycontrol_colaborador_asistencia_marcado_')]
class MarcadoController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'marcarasistencia')]
    public function marcarAsistencia(): Response
    {
        return $this->render('easy_control/colaborador/asistencia/marcado.html.twig');
    }
    #[Route('/api/asistencia', name: 'api_asistencia_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Crear una nueva instancia de la entidad Asistencia
        $asistencia = new Asistencia();
        $asistencia->setAsiFechaentrada(new \DateTime($data['asi_fechaentrada']));
        $asistencia->setAsiHoraentrada(new \DateTime($data['asi_horaentrada']));
        $asistencia->setAsiFotoentrada($data['asi_fotoentrada']);
        $asistencia->setAsiEstadoentrada($data['asi_estadoentrada']);
        $asistencia->setAsiUbicacionentrada([$data['latitud'], $data['longitud']]);
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
        $asistencia->setAsiUbicacionsalida([$data['latitud'], $data['longitud']]);

        $this->entityManager->flush();

        return $this->json($asistencia, Response::HTTP_OK);
    }

}