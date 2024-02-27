<?php

namespace App\Controller\EasyControl\Colaborador\Asistencia;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/marcado', name: 'app_easycontrol_colaborador_asistencia_marcado_')]
class MarcadoController extends AbstractController
{
    #[Route('/', name: 'marcarasistencia')]
    public function marcarAsistencia(): Response
    {
        return $this->render('easy_control/colaborador/asistencia/marcado.html.twig');
    }
}