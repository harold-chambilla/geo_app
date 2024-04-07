<?php

namespace App\Controller\Empresa;

use App\Repository\AsistenciaRepository;
use App\Repository\ColaboradorRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/api/list/asistencia/{fecha}', name: 'api_asis_get_fecha', methods: ['GET'])]
    public function listAsistenciaFecha($fecha, ColaboradorRepository $colaboradorRepository): JsonResponse
    {
        $colaboradores = $colaboradorRepository->findAll();

        $user = $this->getUser();

        $fechaDateTime = new DateTime($fecha);

        // if (!$user) { // Este if debe incluir que solo el super usuario debe poder ingresar a esta sección
        //     return new JsonResponse(['message' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
        // }

        if (!$fechaDateTime) {
            return new JsonResponse(['message' => 'La fecha proporcionada no es válida'], Response::HTTP_BAD_REQUEST);
        }

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
            $asistenciasColaborador = [];

            foreach ($asistencias as $asistencia) {
                if ($asistencia->getAsiFechaentrada()->format('d-m-Y') === $fecha) {
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

            if (!$asistenciasColaborador) {
                return new JsonResponse(['message' => 'No hay una asistencia con esa fecha.'], Response::HTTP_NOT_FOUND);
            }        
        }

        return $this->json($asistenciasColaborador, Response::HTTP_OK);
    }

    #[Route('/api/colaborador/{dni}', name: 'api_colaborador_por_dni', methods: ['GET'])]
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

    #[Route('/api/colaborador/vacio/{fecha}', name: 'api_colaborador_vacio_por_dni', methods: ['GET'])]
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
}
