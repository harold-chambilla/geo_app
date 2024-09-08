<?php

namespace App\Controller\Empresa;

use App\Entity\Colaborador;
use App\Entity\Empresa;
use App\Funciones\Empresa\EmpresaFunciones;
use App\Repository\AreaRepository;
use App\Repository\AsistenciaRepository;
use App\Repository\ColaboradorRepository;
use App\Repository\ConfiguracionAsistenciaRepository;
use App\Repository\EmpresaRepository;
use App\Repository\GrupoRepository;
use App\Repository\HorarioTrabajoRepository;
use App\Repository\PuestoRepository;
use App\Repository\SedeRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Func;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/empresa', name: 'app_empresa_')]
class EmpresaController extends AbstractController
{
    public function __construct(
        private EmpresaFunciones $empresaFunciones
    ) {}

    #[Route('/', name: 'mostrar')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        return $this->redirectToRoute('app_empresa_inicio_mostrar');
    }

    #[Route('/api/asistencia', name: 'api_adm_asistencia', methods: ['GET'])]
    public function listAsistencia(ColaboradorRepository $colaboradorRepository): JsonResponse
    {
        $colaboradores = $colaboradorRepository->findAll();

        $user = $this->getUser();

        // if (!$user) { // Este if debe incluir que solo el super usuario debe poder ingresar a esta sección
        //     return new JsonResponse(['message' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
        // }

        if (empty($colaboradores)) {
            return new JsonResponse(['message' => 'No se encontraron colaboradores'], Response::HTTP_NOT_FOUND);
        }

        foreach ($colaboradores as $colaborador) {
            $asistencias = $colaborador->getColAsistencias();
            $horaTrabajo = $colaborador->getColHorariostrabajo();
            $confAsis = $colaborador->getColEmpresa()->getEmpConfiguracionesAsistencia();

            foreach ($confAsis as $conf) {
                $modalidad = $conf->getCasModalidad();
            }

            foreach ($horaTrabajo as $horaTb) {
                $horaOriginal = $horaTb->getHotHorasalida();
            }

            foreach ($asistencias as $asistencia) {
                $asistenciasColaborador[] =  [
                    'nombre' => $colaborador->getColNombres() . ' ' . $colaborador->getColApellidos() ?? null,
                    'puesto' => $colaborador->getColPuesto() ?? null,
                    'area' => $colaborador->getColArea() ?? null,
                    'fecha' => $asistencia->getAsiFechaentrada()->format('d/m/Y') ?? null,
                    'ingreso' => $asistencia->getAsiHoraentrada()->format('H:i:s') ?? null,
                    'salida' => $asistencia->getAsiHorasalida()->format('H:i:s') ?? null,
                    'modalidad' => $modalidad ?? null,
                    'hora_extra' => $horaOriginal->diff($asistencia->getAsiHorasalida())->format('%H:%I:%s') ?? null
                ];
            }
        }


        return $this->json($asistenciasColaborador, Response::HTTP_OK);
    }


    #[Route('/api/asistencia/filtros', name: 'api_adm_asistencia_filt', methods: ['GET'])]
    public function listAsistenciaFiltss(ColaboradorRepository $colaboradorRepository, Request $request): JsonResponse
    {
        // Obtener los parámetros de la solicitud
        $search = $request->query->get('query');
        $fecha = $request->query->get('fecha');
        $estadoEntrada = $request->query->get('estado_entrada');
        $estadoSalida = $request->query->get('estado_salida');
        $puesto = $request->query->get('puesto');
        $area = $request->query->get('area');
        $modalidad = $request->query->get('modalidad');

        // Obtener las asistencias filtradas
        $asistenciasFiltradas = [];

        // Obtener todos los colaboradores
        $colaboradores = $colaboradorRepository->findAll();

        foreach ($colaboradores as $colaborador) {
            $asistencias = $colaborador->getColAsistencias();

            foreach ($asistencias as $asistencia) {
                // Verificar los filtros
                if (($fecha === null || $asistencia->getAsiFechaentrada()->format('Y-m-d') === $fecha)
                    && ($puesto === null || $colaborador->getColPuesto() === $puesto)
                    && ($area === null || $colaborador->getColArea() === $area)
                    && ($modalidad === null || $colaborador->getColEmpresa()->getEmpConfiguracionesAsistencia()->first()->getCasModalidad() === $modalidad)
                    && ($estadoEntrada === null || $asistencia->getAsiEstadoentrada() === $estadoEntrada)
                    && ($estadoSalida === null || $asistencia->getAsiEstadosalida() === $estadoSalida)
                    && ($search === null || $this->matchesSearchCriteria($colaborador, $asistencia, $search))
                ) {
                    $asistenciasFiltradas[] = [
                        'nombre' => $colaborador->getColNombres() . ' ' . $colaborador->getColApellidos(),
                        'puesto' => $colaborador->getColPuesto(),
                        'area' => $colaborador->getColArea(),
                        'fecha' => $asistencia->getAsiFechaentrada()->format('d/m/Y'),
                        'ingreso' => $asistencia->getAsiHoraentrada()->format('H:i:s'),
                        'salida' => $asistencia->getAsiHorasalida()->format('H:i:s'),
                        'modalidad' => $colaborador->getColEmpresa()->getEmpConfiguracionesAsistencia()->first()->getCasModalidad(),
                        // 'hora_extra' => $horaOriginal->diff($asistencia->getAsiHorasalida())->format('%H:%I:%s'),
                    ];
                }
            }
        }

        return $this->json($asistenciasFiltradas, Response::HTTP_OK);
    }

    // #[Route('/api/asistencia/{fecha}', name: 'api_adm_asistencia_fecha', methods: ['GET'])]
    // public function listAsistenciaFecha($fecha, ColaboradorRepository $colaboradorRepository): JsonResponse
    // {
    //     $colaboradores = $colaboradorRepository->findAll();

    //     $user = $this->getUser();

    //     $fechaDateTime = new DateTime($fecha);

    //     // if (!$user) { // Este if debe incluir que solo el super usuario debe poder ingresar a esta sección
    //     //     return new JsonResponse(['message' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
    //     // }

    //     if (!$fechaDateTime) {
    //         return new JsonResponse(['message' => 'La fecha proporcionada no es válida'], Response::HTTP_BAD_REQUEST);
    //     }

    //     if (empty($colaboradores)) {
    //         return new JsonResponse(['message' => 'No se encontraron colaboradores'], Response::HTTP_NOT_FOUND);
    //     }

    //     $asistenciasColaborador = [];

    //     foreach ($colaboradores as $colaborador) {
    //         $asistencias = $colaborador->getColAsistencias();
    //         $horaTrabajo = $colaborador->getColHorariostrabajo();
    //         $confAsis = $colaborador->getColEmpresa()->getEmpConfiguracionesAsistencia();

    //         foreach ($confAsis as $conf) {
    //             $modalidad = $conf->getCasModalidad();
    //         }

    //         foreach ($horaTrabajo as $horaTb) {
    //             $horaOriginal = $horaTb->getHotHorasalida();
    //         }


    //         foreach ($asistencias as $asistencia) {
    //             if ($asistencia->getAsiFechaentrada()->format('Y-m-d') === $fecha) {
    //                 $asistenciasColaborador[] =  [
    //                     'nombre' => $colaborador->getColNombres() . ' ' . $colaborador->getColApellidos() ?? null,
    //                     'puesto' => $colaborador->getColPuesto() ?? null,
    //                     'area' => $colaborador->getColArea() ?? null,
    //                     'fecha' => $asistencia->getAsiFechaentrada()->format('d/m/Y') ?? null,
    //                     'ingreso' => $asistencia->getAsiHoraentrada()->format('H:i:s') ?? null,
    //                     'salida' => $asistencia->getAsiHorasalida()->format('H:i:s') ?? null,
    //                     'modalidad' => $modalidad ?? null,
    //                     'hora_extra' => $horaOriginal->diff($asistencia->getAsiHorasalida())->format('%H:%I:%s') ?? null
    //                 ];
    //             }
    //         }

    //         if (!$asistenciasColaborador) {
    //             return new JsonResponse(['message' => 'No hay una asistencia con esa fecha.'], Response::HTTP_NOT_FOUND);
    //         }        
    //     }

    //     return $this->json($asistenciasColaborador, Response::HTTP_OK);
    // }

    #[Route('/api/asistencia/filtr', name: 'api_adm_asistencia_fi', methods: ['GET'])]
    public function listAsistenciaFilt(ColaboradorRepository $colaboradorRepository, Request $request): JsonResponse
    {
        // Obtener los parámetros de filtro
        $area = $request->query->get('area') ?? null;
        $puesto = $request->query->get('puesto') ?? null;
        $estadoIngreso = $request->query->get('estado_ingreso') ?? null;
        $estadoSalida = $request->query->get('estado_salida') ?? null;
        // $modalidad = $request->request->get('modalidad');
        $fecha = $request->query->get('fecha') ?? null;

        // Validar que la fecha esté en el formato correcto
        try {
            $fechaDateTime = new DateTime($fecha);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'La fecha proporcionada no es válida'], Response::HTTP_BAD_REQUEST);
        }

        $colaboradores = $colaboradorRepository->findByFilters($area, $puesto, $estadoIngreso, $estadoSalida, $fecha);

        if (empty($colaboradores)) {
            return new JsonResponse(['message' => 'No se encontraron colaboradores'], Response::HTTP_NOT_FOUND);
        }

        foreach ($colaboradores as $colaborador) {
            $asistencias = $colaborador->getColAsistencias();
            $horaTrabajo = $colaborador->getColHorariostrabajo();
            $confAsis = $colaborador->getColEmpresa()->getEmpConfiguracionesAsistencia();

            foreach ($confAsis as $conf) {
                $modalidad = $conf->getCasModalidad();
            }

            foreach ($horaTrabajo as $horaTb) {
                $horaOriginal = $horaTb->getHotHorasalida();
            }

            foreach ($asistencias as $asistencia) {
                $asistenciasColaborador[] =  [
                    'nombre' => $colaborador->getColNombres() . ' ' . $colaborador->getColApellidos() ?? null,
                    'puesto' => $colaborador->getColPuesto() ?? null,
                    'area' => $colaborador->getColArea() ?? null,
                    'fecha' => $asistencia->getAsiFechaentrada()->format('d/m/Y') ?? null,
                    'ingreso' => $asistencia->getAsiHoraentrada()->format('H:i:s') ?? null,
                    'salida' => $asistencia->getAsiHorasalida()->format('H:i:s') ?? null,
                    'modalidad' => $modalidad ?? null,
                    'hora_extra' => $horaOriginal->diff($asistencia->getAsiHorasalida())->format('%H:%I:%s') ?? null
                ];
            }
        }

        return $this->json($asistenciasColaborador, Response::HTTP_OK);
    }

    #[Route('/api/asistencia/{dni}', name: 'api_adm_asistencia_dni', methods: ['GET'])]
    public function obtenerColaboradorPorDni($dni, ColaboradorRepository $colaboradorRepository): JsonResponse
    {
        // Buscar el colaborador por el DNI
        $colaborador = $colaboradorRepository->findOneBy(['col_dninit' => $dni]);

        // Verificar si se encontró el colaborador
        if (!$colaborador) {
            return new JsonResponse(['message' => 'No se encontró ningún colaborador con el DNI proporcionado'], Response::HTTP_NOT_FOUND);
        }

        // Extraer los datos del colaborador
        $datosColaborador = [
            'usuario' => $colaborador->getColNombres() . ' ' . $colaborador->getColApellidos() ?? null,
            'dni' => $colaborador->getColDninit() ?? null,
            'area' => $colaborador->getColArea() ?? null,
        ];

        return $this->json($datosColaborador, Response::HTTP_OK);
    }

    #[Route('/api/asistencia/vacio/{fecha}', name: 'api_adm_asistencia_vacio_fecha', methods: ['GET'])]
    public function obtenerColaboradorVacio($fecha, ColaboradorRepository $colaboradorRepository, AsistenciaRepository $asistenciaRepository): JsonResponse
    {
        $colaboradores = $colaboradorRepository->findAll();

        // Verificar si se encontraron colaboradores
        if (empty($colaboradores)) {
            return new JsonResponse(['message' => 'No se encontraron colaboradores'], Response::HTTP_NOT_FOUND);
        }

        // Preparar un array para almacenar los datos de los colaboradores
        $datosColaborador = [];

        // Recorrer cada colaborador y preparar los datos
        foreach ($colaboradores as $colaborador) {
            // Datos básicos del colaborador
            $asistencia = $asistenciaRepository->findAsistenciaByFecha($colaborador, $fecha);

            $datosColaborador[] = [
                'nombre' => $colaborador->getColNombres(),
                'apellido' => $colaborador->getColApellidos(),
                'dni' => $colaborador->getColDninit(),
                'puesto' => $colaborador->getColPuesto(),
                'area' => $colaborador->getColArea(),
                'fecha' => $fecha, // Fecha específica
                'hora_entrada' => $asistencia ? ($asistencia->getAsiHoraentrada() ? $asistencia->getAsiHoraentrada()->format('H:i:s') : null) : null,
                'hora_salida' => $asistencia ? ($asistencia->getAsiHorasalida() ? $asistencia->getAsiHorasalida()->format('H:i:s') : null) : null,
            ];

            // Agregar los datos del colaborador al array
            // $datosColaboradores[] = $datosColaborador;
        }

        return $this->json($datosColaborador, Response::HTTP_OK);
    }

    #[Route('/api/admin/create-user', name: 'api_create_user', methods: ['POST'])]
    public function createUser(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        ColaboradorRepository $colaboradorRepository,
        EmpresaRepository $empresaRepository,
    ): JsonResponse {
        $admin = $this->getUser();

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
        $correoElectronico = $request->request->get('correo_electronico');
        $nombreUsuario = $request->request->get('nombre_usuario');
        $password = $request->request->get('password');

        if (empty($nombre) || empty($apellidos) || empty($dni) || empty($correoElectronico) || empty($nombreUsuario) || empty($password)) {
            return new JsonResponse(['error' => 'Todos los campos son obligatorios'], Response::HTTP_BAD_REQUEST);
        }

        if ($colaboradorRepository->findOneBy(['col_nombreusuario' => $nombreUsuario])) {
            return new JsonResponse(['error' => 'El nombre de usuario ya existe'], Response::HTTP_CONFLICT);
        }

        if ($colaboradorRepository->findOneBy(['col_correoelectronico' => $correoElectronico])) {
            return new JsonResponse(['error' => 'El correo electrónico ya está en uso'], Response::HTTP_CONFLICT);
        }

        $nuevoColaborador = new Colaborador();
        $nuevoColaborador->setColNombres($nombre);
        $nuevoColaborador->setColApellidos($apellidos);
        $nuevoColaborador->setColDninit($dni);
        $nuevoColaborador->setColFechanacimiento(new \DateTime($fechaNacimiento));
        $nuevoColaborador->setColArea($area);
        $nuevoColaborador->setColCorreoelectronico($correoElectronico);
        $nuevoColaborador->setColNombreusuario($nombreUsuario);
        $nuevoColaborador->setPassword($passwordHasher->hashPassword($nuevoColaborador, $password));
        $nuevoColaborador->setRoles(['ROLE_EMPLEADO']);
        $nuevoColaborador->setColEmpresa($admin->getColEmpresa());

        $entityManager->persist($nuevoColaborador);
        $entityManager->flush();

        return $this->json(['success' => 'Usuario creado exitosamente'], Response::HTTP_CREATED);
    }

    #[Route('/api/{id}/registros', name: 'api_empresa_registros', methods: ['GET'])]
    public function obtenerRegistrosPorEmpresa(
        Empresa $empresa,
        ColaboradorRepository $colaboradorRepository,
        AsistenciaRepository $asistenciaRepository,
        SedeRepository $sedeRepository,
        GrupoRepository $grupoRepository,
        ConfiguracionAsistenciaRepository $configuracionAsistenciaRepository,
        PuestoRepository $puestoRepository,
        HorarioTrabajoRepository $horarioTrabajoRepository,
        AreaRepository $areaRepository
    ): JsonResponse {
        if (!$empresa) {
            return new JsonResponse(['error' => 'Empresa no encontrada.'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Obtener los colaboradores asociados con la empresa, ordenados de manera descendente por ID
        $colaboradores = $colaboradorRepository
            ->findBy(['col_empresa' => $empresa], ['id' => 'DESC']);

        // Obtener solo los IDs de los colaboradores
        $colaboradorIds = array_map(function ($colaborador) {
            $data = [
                'id' => $colaborador->getId(),
                'nombres' => $colaborador->getColNombres(),
            ];
            return $data;
        }, $colaboradores);


        $colaboradoresData = [];

        foreach ($colaboradores as $colaborador) {
            // Obtener las asistencias de cada colaborador, ordenadas de manera descendente por ID
            $data = [
                'id' => $colaborador->getId(),
                'nombres' => $colaborador->getColNombres(),
            ];
            $asistencias = $asistenciaRepository
                ->findBy(['asi_colaborador' => $colaborador->getId()], ['id' => 'DESC']);

            // Obtener los IDs de las asistencias
            $asistenciaIds = array_map(function ($asistencia) {
                return $asistencia->getId();
            }, $asistencias);

            // Añadir los datos del colaborador y sus asistencias al arreglo
            $horariosTrabajo = $horarioTrabajoRepository
                ->findBy(['hot_colaborador' => $colaborador->getId()], ['id' => 'DESC']);

            // Obtener los IDs de los horarios de trabajo
            $horarioTrabajoIds = array_map(function ($horario) {
                return $horario->getId();
            }, $horariosTrabajo);

            // Obtener el puesto del colaborador (asumimos que cada colaborador tiene un puesto)
            $puesto = $colaborador->getColPuesto(); // Asegúrate de que este método existe en la entidad `Colaborador`
            $puestoId = $puesto ? $puesto->getId() : null;

            // Añadir los datos del colaborador, sus asistencias, horarios de trabajo y puesto al arreglo
            $colaboradoresData[] = [
                // 'colaborador_id' => $colaborador->getId(),
                'colaborador_id' => $data,
                'asistencias' => $asistenciaIds,
                'horarios_trabajo' => $horarioTrabajoIds,
                'puesto_id' => $puestoId,
            ];
        }

        $asistencias = $asistenciaRepository
            ->createQueryBuilder('a')
            ->where('a.asi_colaborador IN (:colaboradorIds)')
            ->setParameter('colaboradorIds', $colaboradorIds)
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult();

        $asistenciaIds = array_map(function ($asistencia) {
            return $asistencia->getId();
        }, $asistencias);

        $sedes = $sedeRepository->findBy(['sed_empresa' => $empresa], ['id' => 'DESC']);

        $sedeIds = array_map(function ($sede) {
            return $sede->getId();
        }, $sedes);

        $grupos = $grupoRepository->findBy(['grp_empresa' => $empresa], ['id' => 'DESC']);

        $grupoIds = array_map(function ($grupo) {
            return $grupo->getId();
        }, $grupos);

        $areas = $areaRepository
            ->findBy(['ara_empresa' => $empresa], ['id' => 'DESC']);

        $areasData = [];

        foreach ($areas as $area) {
            // Obtener los puestos asociados con el área
            $puestos = $puestoRepository
                ->findBy(['pst_area' => $area], ['id' => 'DESC']);

            // Obtener los IDs de los puestos
            $puestoIds = array_map(function ($puesto) {
                return $puesto->getId();
            }, $puestos);

            // Añadir los datos del área y sus puestos al arreglo
            $areasData[] = [
                'area_id' => $area->getId(),
                'puestos' => $puestoIds,
            ];
        }

        $configuracionesAsistencia =  $configuracionAsistenciaRepository
            ->findBy(['cas_empresa' => $empresa], ['id' => 'DESC']);

        $configuracionAsistenciaIds = array_map(function ($configuracionAsistencia) {
            return $configuracionAsistencia->getId();
        }, $configuracionesAsistencia);

        $data = [
            'empresa_id' => $empresa->getId(),
            // 'colaboradores' => $colaboradorIds,
            'colaboradoresData' => $colaboradoresData,
            'asistencias' => $asistenciaIds,
            'sedes' => $sedeIds,
            'grupos' => $grupoIds,
            'areas' => $areasData,
            'configuraciones_asistencia' => $configuracionAsistenciaIds,
        ];

        return $this->json($data);
    }

    private function matchesSearchCriteria($colaborador, $asistencia, $search)
    {
        return !$search || stripos($asistencia->getAsiEstadoentrada(), $search) !== false ||
            stripos($asistencia->getAsiEstadosalida(), $search) !== false ||
            stripos($asistencia->getAsiFechaentrada()->format('Y-m-d'), $search) !== false ||
            stripos($colaborador->getColPuesto(), $search) !== false ||
            stripos($colaborador->getColEmpresa()->getEmpConfiguracionesAsistencia()->first()->getCasModalidad(), $search) !== false ||
            stripos($colaborador->getColArea(), $search) !== false;
    }
}
