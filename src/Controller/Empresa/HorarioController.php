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

#[Route('/empresa/horario', name: 'app_empresa_horario_')]
class HorarioController extends AbstractController 
{

    public function __construct(
        private EmpresaFunciones $empresaFunciones,
        private AreaFunciones $areaFunciones
    ){}    

	#[Route('/', name: 'mostrar')]
	public function mostrar(): Response
	{
		return $this->render('empresa/horario/index.html.twig');
	}

    #[Route('/api/areas-empleados', name: 'obtener_areas_empleados')]
    public function obtenerAreasEmpleados(): JsonResponse
    {
        // Obtener la empresa actual utilizando empresaFunciones
        $empresa = $this->empresaFunciones->obtenerEmpresa();

        // Verificar si la empresa fue encontrada
        if ($empresa) {
            // Llamar a la función obtenerEmpleadosPorAreaYPuesto y devolver el resultado en formato JSON
            $resultado = $this->areaFunciones->obtenerEmpleadosPorAreaYPuesto($empresa);
            return $this->json($resultado, JsonResponse::HTTP_OK);
        }

        // Si no se encontró la empresa, devolver un error 404
        return $this->json(['error' => 'Empresa no encontrada'], JsonResponse::HTTP_NOT_FOUND);
    }
}
