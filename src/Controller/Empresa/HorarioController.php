<?php

namespace App\Controller\Empresa;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/empresa/horario', name: 'app_empresa_horario_')]
class HorarioController extends AbstractController
{
    #[Route('/', name: 'mostrar')]
    public function index(): Response
    {
        return $this->render('empresa/horario/index.html.twig');
    }
}
