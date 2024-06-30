<?php

namespace App\Controller\Test;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Map2Controller extends AbstractController
{
    #[Route('/map2', name: 'app_map2')]
    public function index(): Response
    {
        return $this->render('map2/index.html.twig', [
            'controller_name' => 'Map2Controller',
        ]);
    }
}
