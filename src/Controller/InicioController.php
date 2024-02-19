<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;

class InicioController extends AbstractController
{
    #[Route('/inicio', name: 'app_inicio')]
    public function index(NotifierInterface $notificador): Response
    {
        // $notificacion = new Notification('Hola OneSignal', [], ['onesignal']);
        // $notificador->send($notificacion);
        return $this->render('inicio/index.html.twig', [
            'controller_name' => 'InicioController',
        ]);
    }

    public function sendNotification(NotifierInterface $notifier)
    {
        $notificacion = new Notification('Hola OneSignal', ['onesignal']);
        $notifier->send($notificacion);
    }
}
