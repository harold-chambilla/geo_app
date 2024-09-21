<?php 

namespace App\Controller\Empresa;

use App\Entity\Colaborador;
use App\Entity\Empresa;
use App\Funciones\Empresa\AreaFunciones;
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
        private EmpresaFunciones $empresaFunciones,
        private AreaFunciones $areaFunciones
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

    #[Route('/api/areas', name: 'obtener_areas')]
	public function obtenerAreas(): JsonResponse
    {	

		if($this->empresaFunciones->obtenerEmpresa()){
			return $this->json($this->areaFunciones->obtenerAreas($this->empresaFunciones->obtenerEmpresa()));
		}
		return $this->json(null, JsonResponse::HTTP_NOT_FOUND);
    }

    #[Route('/api/registrar-areas', name: 'registrar_areas', methods: ['POST'])]
    public function registrarAreas(Request $request): JsonResponse
    {
        // Obtener la empresa del colaborador autenticado
        $empresa = $this->empresaFunciones->obtenerEmpresa();

        // Verificar si la empresa fue encontrada
        if (!$empresa) {
            return new JsonResponse(['error' => 'Empresa no encontrada'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Obtener el contenido JSON del cuerpo de la solicitud
        $areas = json_decode($request->getContent(), true);

        // Verificar si los datos de las áreas son válidos
        if (!$areas || !is_array($areas)) {
            return new JsonResponse(['error' => 'Datos inválidos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Llamar a la función para registrar las áreas y puestos
        $resultado = $this->areaFunciones->registroAreasYPuestos($areas, $empresa);

        // Verificar si la función de registro devuelve 'Ok' o un error
        if ($resultado === 'Ok') {
            return new JsonResponse(['message' => 'Áreas y puestos registrados correctamente'], JsonResponse::HTTP_OK);
        } else {
            return new JsonResponse(['error' => $resultado], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
