<?php

namespace App\Controller\Empresa;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/empresa/inicio', name: 'app_empresa_inicio_')]
class InicioController extends AbstractController
{
    public function __construct(){}
    
    #[Route('/', name: 'inicio')]
    public function index(): Response
    {
        return $this->render('empresa/inicio.html.twig');
    }
}
