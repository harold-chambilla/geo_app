<?php

namespace App\Controller\EasyControl\General;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeneralController extends AbstractController
{
    #[Route('/', name: 'app_easycontrol_general_redireccion')]
    public function redireccion(): Response
    {
        return $this->redirectToRoute('app_easycontrol_colaborador_asistencia_marcado_marcarasistencia');
    }
}