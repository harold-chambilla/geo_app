<?php

namespace App\Controller\Empresa;

use App\Form\CargadorArchivoType;
use App\Funciones\Empresa\EmpleadosFunciones;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Route('/empresa/empleados', name: 'app_empresa_empleados_')]
class EmpleadosController extends AbstractController
{
    private string $uploadDirectory = 'uploads/empresa/empleados';
    private string $downloadDirectory = 'downloads/empresa/empleados';

    # Formulario de registro simple
    #[Route('/', name: 'mostrar')]
    public function index(): Response
    {
        return $this->render('empresa/empleados/index.html.twig');
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

            $this->addFlash('success', 'El archivo se cargó correctamente: ' . $employeesFile['name']);
            return $this->redirectToRoute('app_empresa_empleados_cargar');
        }

        return $this->render('empresa/empleados/carga.html.twig', [
            'form' => $form,
            'employeesFile' => $employeesFile,
            'resultado' => $empleadosFunciones->listadoColaboradores($empleadosFunciones->extraer('formato-empleados-661c5e4271a25.xlsx', $this->uploadDirectory))
        ]);
    }

    # Descarga de modelo
    #[Route('/descargar/{archivo}', name: 'descargar')]
    public function descargar($archivo, EmpleadosFunciones $empleadosFunciones): BinaryFileResponse
    {
        return $empleadosFunciones->descargar($archivo, $this->downloadDirectory);
    }
}
