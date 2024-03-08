<?php

namespace App\Controller\EasyControl\Colaborador\Seguridad;

use App\Entity\Colaborador;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

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

    #[Route('/api/acceder', name: 'app_easycontrol_colaborador_seguridad_api_acceder', methods: ['POST'])]
    public function accederApi(#[CurrentUser] ?Colaborador $user): Response
    {
        if(null === $user){
            return $this->json([
                'message' => 'Faltan credenciales',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = base64_encode(random_bytes(32));

        return $this->json([
            'user' => $user->getUserIdentifier(),
            'token' => $token
        ]);
    }
}
