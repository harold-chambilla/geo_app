<?php

namespace App\Controller\Empresa;

use App\Entity\Colaborador;
use App\Form\Empresa\CargadorArchivoType;
use App\Funciones\Empresa\EmpleadosFunciones;
use App\Funciones\Empresa\EmpresaFunciones;
use App\Repository\AreaRepository;
use App\Repository\ColaboradorRepository;
use App\Repository\PuestoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

#[Route('/empresa/empleados', name: 'app_empresa_empleados_')]
class EmpleadosController extends AbstractController
{
    private string $uploadDirectory = 'uploads/empresa/empleados';
    private string $downloadDirectory = 'downloads/empresa/empleados';

    public function __construct(    
        private EmpresaFunciones $empresaFunciones,
        private EmpleadosFunciones $empleadosFunciones,
        private ColaboradorRepository $colaboradorRepository,
        private AreaRepository $areaRepository,
        private PuestoRepository $puestoRepository,
        private UserPasswordHasherInterface $userPasswordHasherInterface,
        private EntityManagerInterface $entityManagerInterface
    ){}

    #[Route('/', name: 'mostrar')]
    public function index(Request $request): Response
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
                $employeesFile['name'] = $this->empleadosFunciones->cargar($employeesFile['data'], $this->uploadDirectory);
            }

            $datos = $this->empleadosFunciones->extraer($employeesFile['name'], $this->uploadDirectory);
            $colaboradores = $this->empleadosFunciones->listadoColaboradores($datos);
            $registro = $this->empleadosFunciones->registroMasivo($colaboradores);

            $this->addFlash('success', 'El archivo se cargó correctamente: ' . $employeesFile['name']);
            return $this->redirectToRoute('app_empresa_empleados_cargar');
        }        
        
        return $this->render('empresa/empleados/index.html.twig', [
            'form' => $form,
            'contenido' => [
                'areas' => $this->empresaFunciones->obtenerAreas($this->empresaFunciones->obtenerEmpresa()),
                'sedes' => $this->empresaFunciones->obtenerSedes($this->empresaFunciones->obtenerEmpresa())    
            ]
        ]);
    }

    #[Route('/descargar/{archivo}', name: 'descargar')]
    public function descargar($archivo, EmpleadosFunciones $empleadosFunciones): BinaryFileResponse
    {
        return $empleadosFunciones->descargar($archivo, $this->downloadDirectory);
    }

    #[Route('/api/registro', name: 'api_crear', methods: ['POST'])]
    public function registroIndividual(Request $request): JsonResponse {
        $admin = $this->getUser();
        $admin = $this->colaboradorRepository->findOneBy(['col_nombreusuario' => $admin->getUserIdentifier()]);
        
        if (!$admin) {
            return new JsonResponse(['error' => 'Acceso denegado'], Response::HTTP_UNAUTHORIZED);
        }

        if (!in_array('ROLE_SUPERADMIN', $admin->getRoles())) {
            return new JsonResponse(['error' => 'No tienes permisos para realizar esta acción'], Response::HTTP_FORBIDDEN);
        }

        $nombre = $request->request->get('nombre');
        $apellidos = $request->request->get('apellidos');
        $dni = $request->request->get('dni');
        $fechaNacimiento = $request->request->get('fecha_nacimiento');
        $area = $request->request->get('area');
        $puesto = $request->request->get('puesto');
        $correoElectronico = $request->request->get('correo_electronico');
        $nombreUsuario = $request->request->get('nombre_usuario');
        $password = $request->request->get('password');
        $roles = json_decode($request->request->get('rol'), true);
        $sede = $request->request->get('sede');

        if (empty($nombre) || empty($apellidos) || empty($dni) || empty($correoElectronico) || empty($nombreUsuario) || empty($password) || empty($roles)) {
            return new JsonResponse(['error' => 'Todos los campos son obligatorios'], Response::HTTP_BAD_REQUEST);
        }

        if ($this->colaboradorRepository->findOneBy(['col_nombreusuario' => $nombreUsuario])) {
            return new JsonResponse(['error' => 'El nombre de usuario ya existe'], Response::HTTP_CONFLICT);
        }

        if ($this->colaboradorRepository->findOneBy(['col_correoelectronico' => $correoElectronico])) {
            return new JsonResponse(['error' => 'El correo electrónico ya está en uso'], Response::HTTP_CONFLICT);
        }

        $nuevoColaborador = new Colaborador();
        $nuevoColaborador->setColNombres($nombre);
        $nuevoColaborador->setColApellidos($apellidos);
        $nuevoColaborador->setColDninit($dni);
        $nuevoColaborador->setColFechanacimiento(new \DateTime($fechaNacimiento));
        $nuevoColaborador->setColArea($area ? $this->areaRepository->findOneBy(['id' => $area])->getAraNombre() : null);
        $nuevoColaborador->setColPuesto($puesto ? $this->puestoRepository->findOneBy(['id' => $puesto]) : null);
        $nuevoColaborador->setColCorreoelectronico($correoElectronico);
        $nuevoColaborador->setColNombreusuario($nombreUsuario);
        $nuevoColaborador->setPassword($this->userPasswordHasherInterface->hashPassword($nuevoColaborador, $password));
        $nuevoColaborador->setRoles($roles);
        $nuevoColaborador->setColEmpresa($admin->getColEmpresa());
        
        $nuevoColaborador->setColEliminado(false);
        $nuevoColaborador->setColGrupo($admin->getColGrupo());
        
        $this->entityManagerInterface->persist($nuevoColaborador);
        $this->entityManagerInterface->flush();

        return $this->json(['success' => 'Usuario creado exitosamente'], Response::HTTP_CREATED);
    }
}
