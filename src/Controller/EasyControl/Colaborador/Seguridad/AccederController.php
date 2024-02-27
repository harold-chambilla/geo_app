<?php

namespace App\Controller\EasyControl\Colaborador\Seguridad;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccederController extends AbstractController
{
    #[Route(path: '/acceder', name: 'app_easycontrol_colaborador_seguridad_acceder')]
    public function acceder(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('easy_control/colaborador/seguridad/acceder.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/desconectarse', name: 'app_easycontrol_colaborador_seguridad_desconectarse')]
    public function desconectarse(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
