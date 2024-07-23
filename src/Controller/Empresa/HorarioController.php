<?php 

namespace App\Controller\Empresa;

use App\Entity\Colaborador;
use App\Entity\Empresa;
use App\Funciones\Empresa\EmpresaFunciones;
use App\Repository\ColaboradorRepository;
use App\Repository\EmpresaRepository;
use Doctrine\ORM\Query\Expr\Func;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/empresa/Horario', name: 'app_empresa_Horario_')]
class HorarioController extends AbstractController 
{	

	#[Route('/', name: 'mostrar')]
	public function mostrar(): Response
	{
		return $this->render('empresa/Horario/index.html.twig');
	}

}

<?php

namespace App\Controller\Empresa;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/empresa/horario', name: 'app_empresa_horario_')]
class HorarioController extends AbstractController
{
    #[Route('/', name: 'mostrar')]
    public function index(): Response
    {
        return $this->render('empresa/horario/index.html.twig');
    }
}
