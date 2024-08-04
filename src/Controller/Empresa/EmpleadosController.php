<?php

namespace App\Controller\Empresa;

use App\Form\Empresa\CargadorArchivoType;
use App\Funciones\Empresa\EmpleadosFunciones;
use App\Repository\ColaboradorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/empresa/empleados', name: 'app_empresa_empleados_')]
class EmpleadosController extends AbstractController
{
    private string $uploadDirectory = 'uploads/empresa/empleados';
    private string $downloadDirectory = 'downloads/empresa/empleados';

	public function __construct(
		private ColaboradorRepository $colaboradorRepository
	){}

    # Formulario de registro simple
    #[Route('/', name: 'mostrar')]
    public function index(Request $request): Response
	{
		$colaborador = $this->colaboradorRepository->findOneBy([
			'col_nombreusuario' => $this->getUser()->getUserIdentifier(),
		]);
		$visible =  explode(' ', $colaborador->getColNombres())[0] . ' ' . explode(' ', $colaborador->getColApellidos())[0];

		return $this->render('empresa/empleados/index.html.twig', [
			'usuario' => $visible
		]);
    }

    # Carga masiva de datos
    #[Route('/cargar', name: 'cargar')]
    public function carga(Request $request, EmpleadosFunciones $empleadosFunciones): Response
    {
        $form = $this->createForm(CargadorArchivoType::class);
        $form->handleRequest($request);
        $employeesFile = [
            'name' => null,
            'data' => null
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            $employeesFile['data'] = $form->get('archivo')->getData();
            if($employeesFile['data']) {
                $employeesFile['name'] = $empleadosFunciones->cargar($employeesFile['data'], $this->uploadDirectory);
            }

            $datos = $empleadosFunciones->extraer($employeesFile['name'], $this->uploadDirectory);
            $colaboradores = $empleadosFunciones->listadoColaboradores($datos);
            $registro = $empleadosFunciones->registroMasivo($colaboradores);

            $this->addFlash('success', 'El archivo se cargó correctamente: ' . $employeesFile['name']);
            return $this->redirectToRoute('app_empresa_empleados_cargar');
        }

        return $this->render('empresa/empleados/carga.html.twig', [
            'form' => $form
        ]);
    }

    # Descarga de modelo
    #[Route('/descargar/{archivo}', name: 'descargar')]
    public function descargar($archivo, EmpleadosFunciones $empleadosFunciones): BinaryFileResponse
    {
        return $empleadosFunciones->descargar($archivo, $this->downloadDirectory);
    }

    //Crear empleado
	#[Route('/api/empleado/crear', name: 'crear_empleado', methods: ['POST'])]
	public function crearEmpleado(Request $request, EmpleadosFunciones $empleadosFunciones): JsonResponse
	{	
		$datos = json_decode($request->getContent(), true);
		if($datos['empleado']){
			//$registro = $empleadosFunciones->registro($datos['empleado']);
			return $this->json(['message' => "Test de API"], JsonResponse::HTTP_OK);
		}
		return $this->json(null, JsonResponse::HTTP_NOT_MODIFIED);
	}

    #[Route('/api/empleado/test', name: 'test', methods: ['GET'])]
	public function tester(Request $request): JsonResponse
	{	
        return $this->json(['message' => "Funciona!"], JsonResponse::HTTP_OK);
	}
}
