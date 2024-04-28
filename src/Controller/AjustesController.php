<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjustesController extends AbstractController
{
    #[Route('/', name: 'app_ajustes_reencaminar')]
    public function redireccion(): Response
    {
        return $this->redirectToRoute('app_colaborador_asistencia_marcado_marcarasistencia');
    }
}