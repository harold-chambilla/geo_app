<?php

namespace App\Controller\Empresa;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/empresa', name: 'app_empresa_inicio_')]
class InicioController extends AbstractController
{
    public function __construct(){}

    #[Route(name: 'reencaminar')]
    public function reencaminar(): Response
    {
        return $this->redirectToRoute('app_empresa_inicio_inicio');
    }
    
    #[Route('/inicio', name: 'inicio')]
    public function index(): Response
    {
        return $this->render('empresa/inicio.html.twig');
    }
}
