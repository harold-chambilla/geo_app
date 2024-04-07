<?php

namespace App\Controller\Empresa;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/empresa', name: 'app_empresa_empleados_')]
class EmpleadosController extends AbstractController
{
    #[Route('/empleados', name: 'mostrar')]
    public function index(): Response
    {
        return $this->render('empresa/empleados/index.html.twig');
    }
}
