<?php

namespace App\Controller\Empresa;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/empresa/horario', name: 'app_empresa_horario_')]
class HorarioController extends AbstractController
{
    public function __construct(){}
    
    #[Route('/', name: 'inicio')]
    public function index(): Response
    {
        return $this->render('empresa/horario.html.twig');
    }
}
