<?php

namespace App\Controller\Empresa;

use App\Form\CargadorArchivoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\Empresa\Empleados\CargadorArchivo;
use App\Service\Empresa\Empleados\DescargadorArchivo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Route('/empresa/empleados', name: 'app_empresa_empleados_')]
class EmpleadosController extends AbstractController
{
    # Formulario de registro simple
    #[Route('/', name: 'mostrar')]
    public function index(): Response
    {
        return $this->render('empresa/empleados/index.html.twig');
    }

    # Carga masiva de datos
    #[Route('/cargar', name: 'cargar')]
    public function carga(Request $request, CargadorArchivo $cargadorArchivo): Response
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
                $employeesFile['name'] = $cargadorArchivo->cargar($employeesFile['data']);
            }

            $this->addFlash('success', 'El archivo se cargó correctamente.');
            return $this->redirectToRoute('app_empresa_empleados_cargar');
        }

        return $this->render('empresa/empleados/carga.html.twig', [
            'form' => $form,
            'employeesFile' => $employeesFile,
        ]);
    }

    # Descarga de modelo
    #[Route('/descargar/{archivo}', name: 'descargar')]
    public function descargar($archivo, DescargadorArchivo $descargadorArchivo): BinaryFileResponse
    {
        return $descargadorArchivo->descargar($archivo);
    }
}
