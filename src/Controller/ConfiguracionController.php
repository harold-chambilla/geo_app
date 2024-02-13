<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function configuracionUsuario(Request $request): Response
    {
        $ip_cliente = $request->getClientIp();
        $identificador_publicidad = null;
        if (isset($_COOKIE['IDFA'])) {
            // Estamos en iOS
            $identificador_publicidad = $_COOKIE['IDFA'];

        } else if (isset($_COOKIE['AAID'])) {
            // Estamos en Android
            $identificador_publicidad = $_COOKIE['AAID'];
        }
        return $this->render('configuracion/usuario.html.twig', [
            'ip_cliente' => $ip_cliente,
            'advertising_id' => $identificador_publicidad,
        ]);
    }
}
