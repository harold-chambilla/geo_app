<?php

namespace App\Controller\Empresa\Seguridad;

use App\Entity\Colaborador;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/empresa', name: 'app_empresa_seguridad_')]
class AccederController extends AbstractController
{
    #[Route(path: '/acceder', name: 'acceder')]
    public function acceder(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('empresa/seguridad/acceder.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/desconectarse', name: 'desconectarse')]
    public function desconectarse(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}