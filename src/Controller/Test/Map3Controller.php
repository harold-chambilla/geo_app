<?php

namespace App\Controller\Test;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Map3Controller extends AbstractController
{
    #[Route('/map3', name: 'app_map3')]
    public function index(): Response
    {
        return $this->render('map3/index.html.twig', [
            'controller_name' => 'Map3Controller',
        ]);
    }
}
