<?php

namespace App\Controller\EasyControl\Colaborador\Asistencia;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/resultado', name: 'app_easycontrol_colaborador_asistencia_resultado_')]
class ResultadoController extends AbstractController
{
    #[Route('/', name: 'verresultado')]
    public function verResultado(): Response
    {
        return $this->render('easy_control/colaborador/asistencia/resultado.html.twig');
    }
}