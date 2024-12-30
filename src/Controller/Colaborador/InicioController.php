<?php

namespace App\Controller\Colaborador;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/inicio', name: 'app_colaborador_inicio_')]
class InicioController extends AbstractController
{
    public function __construct(){}
    
    #[Route(name: 'inicio')]
    public function index(): Response
    {
        return $this->render('colaborador/inicio.html.twig');
    }
}
