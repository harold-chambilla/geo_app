<?php

namespace App\Controller\Empresa;

use App\Repository\AsistenciaRepository;
use App\Repository\ColaboradorRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/empresa', name: 'app_empresa_')]
class EmpresaController extends AbstractController
{
    #[Route('/', name: 'mostrar')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        return $this->render('empresa/index.html.twig');
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
                    && ($search === null || $this->matchesSearchCriteria($colaborador, $asistencia, $search))) {
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
    public function listAsistenciaFilt(ColaboradorRepository $colaboradorRepository,Request $request): JsonResponse 
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


    private function matchesSearchCriteria($colaborador, $asistencia, $search)
    {
        return !$search || stripos($asistencia->getAsiEstadoentrada(), $search) !== false ||
        stripos($asistencia->getAsiEstadosalida(), $search) !== false ||
        stripos($asistencia->getAsiFechaentrada()->format('Y-m-d'), $search) !== false ||
        stripos($colaborador->getColPuesto(), $search) !== false ||
        stripos($colaborador->getColEmpresa()->getEmpConfiguracionesAsistencia()->first()->getCasModalidad(), $search) !== false ||
        stripos($colaborador->getColArea(), $search) !== false
        ;
    }
}
