<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfiguracionController extends AbstractController
{
    #[Route('/', name: 'app_configuracion')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_map2');
    }

    #[Route('/configuracion/usuario', name: 'app_configuracion_usuario')]
    public function configuracionUsuario(): Response
    {
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $direccion_mac = null;
        return $this->render('configuracion/usuario.html.twig', [
            'ip_cliente' => $ip_cliente,
            'direccion_mac' => $direccion_mac,
        ]);
    }
}
