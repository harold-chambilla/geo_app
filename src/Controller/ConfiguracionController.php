<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
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
        $process = new Process(['arp', '-a', $ip_cliente]);
        $direccion_mac = null;
        try {
            // Ejecutar el proceso y obtener la salida
            $process->mustRun();
            $output = $process->getOutput();
            
            // Extraer la dirección MAC de la salida
            preg_match('/([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})/', $output, $matches);
            
            // Devolver la dirección MAC encontrada
            $direccion_mac = isset($matches[0]) ? $matches[0] : null;
        } catch (ProcessFailedException $exception) {
            // No hacer nada.
        }
        return $this->render('configuracion/usuario.html.twig', [
            'ip_cliente' => $ip_cliente,
            'direccion_mac' => $direccion_mac,
        ]);
    }
}
