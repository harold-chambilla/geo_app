<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/', name: 'app_ajustes_')]
class AjustesController extends AbstractController
{
    public function __construct(){}
    
    #[Route(name: 'reencaminar')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_colaborador_inicio_inicio');
    }
}
