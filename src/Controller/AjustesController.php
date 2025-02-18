<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/', name: 'app_ajustes_')]
class AjustesController extends AbstractController
{
    public function __construct(){}
    
    #[Route(name: 'reencaminar')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_colaborador_inicio_mostrar');
    }

    #[Route('tiempo', name: 'obetener_tiempo', methods: ['GET'])]
    public function getDateTime(Request $request): JsonResponse
    {
        $timezone = $request->query->get('timezone', 'America/Lima');
        $locale = $request->query->get('locale', 'ES-PE');
        
        try {
            $dateTime = new \DateTime('now', new \DateTimeZone($timezone));
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid timezone'], Response::HTTP_BAD_REQUEST);
        }
        
        $data = [
            'date' => $this->formatDate($dateTime),
            'time' => $this->formatTime($dateTime),
            'timezone' => $dateTime->getTimezone()->getName(),
            'locale' => $locale
        ];
        
        return new JsonResponse($data);
    }

    private function formatDate(\DateTime $dateTime): string
    {
        return $dateTime->format('d-m-Y');
    }

    private function formatTime(\DateTime $dateTime): string
    {
        return $dateTime->format('H:i:s');
    }
}
