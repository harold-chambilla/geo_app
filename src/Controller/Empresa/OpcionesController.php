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

#[Route('/empresa/opciones', name: 'app_empresa_opciones_')]
class OpcionesController extends AbstractController 
{	
	public function __construct(
		private ColaboradorRepository $colaboradorRepository, 
		private EmpresaFunciones $empresaFunciones
	){}

	#[Route('/', name: 'mostrar')]
	public function mostrar(): Response
	{
		return $this->render('empresa/opciones/index.html.twig');
	}

	#[Route('/api/ruc', name: 'obtener_ruc')]
	public function obteneRuc(): JsonResponse
	{
		if($this->empresaFunciones->obtenerEmpresa()){
			return $this->json($this->empresaFunciones->obtenerEmpresa()->getEmpRuc());
		}
		return $this->json(null, JsonResponse::HTTP_NOT_FOUND);	
	}

	//Obtener razon social
	#[Route('/api/razonsocial', name: 'obtener_razonsocial')]
	public function obtenerRazonSocial(): JsonResponse
	{
		if($this->empresaFunciones->obtenerEmpresa()){
			return $this->json($this->empresaFunciones->obtenerEmpresa()->getEmpNombreempresa());
		}
		return $this->json(null, JsonResponse::HTTP_NOT_FOUND);
	}

	//Obtener sedes y sucursales
	#[Route('/api/sedes', name: 'obtener_sedes')]
	public function obtenerSedes(): JsonResponse
	{	
		if($this->empresaFunciones->obtenerEmpresa()){
			return $this->json($this->empresaFunciones->obtenerEmpresa()->getEmpSedes());
		}
		return $this->json(null, JsonResponse::HTTP_NOT_FOUND);
	}
}
