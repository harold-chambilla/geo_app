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

	//Editar RUC
	#[Route('/api/ruc/modificar', name: 'modificar_ruc', methods: ['POST'])]
	public function modificarRuc(Request $request): JsonResponse
	{	
		$datos = json_decode($request->getContent(), true);
		$empresa = $this->empresaFunciones->obtenerEmpresa();
		if($datos['ruc']){
			$this->empresaFunciones->actualizarRUC($empresa, $datos['ruc']);
			return $this->json(['message' => 'RUC modificado correctamente'], JsonResponse::HTTP_OK);
		}
		return $this->json(null, JsonResponse::HTTP_NOT_MODIFIED);
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

	//Editar Razon social
	#[Route('/api/razonsocial/modificar', name: 'modificar_razonsocial', methods: ['POST'])]
	public function modificarRazonSocial(Request $request): JsonResponse
	{	
		$datos = json_decode($request->getContent(), true);
		$empresa = $this->empresaFunciones->obtenerEmpresa();
		if($datos['razonsocial']){
			$this->empresaFunciones->actualizarRazonSocial($empresa, $datos['razonsocial']);
			return $this->json(['message' => 'Razon social modificada correctamente'], JsonResponse::HTTP_OK);
		}
		return $this->json(null, JsonResponse::HTTP_NOT_MODIFIED);
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

	//Editar sedes y sucursales
	
}
