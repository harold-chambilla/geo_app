<?php

namespace App\Controller\Empresa;

use App\Entity\Puesto;
use App\Function\Empresa\AreaFunction;
use App\Function\Empresa\ColaboradorFunction;
use App\Function\Empresa\SedeFunction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Proxies\__CG__\App\Entity\Sede;

#[Route('/empresa/empleados', name: 'app_empresa_empleados_')]
class EmpleadosController extends AbstractController
{
    public function __construct(
        private ColaboradorFunction $colaboradorFunction,
        private AreaFunction $areaFunction,
        private SedeFunction $sedeFunction
    ){}
    
    #[Route('/', name: 'inicio')]
    public function index(): Response
    {
        return $this->render('empresa/empleados.html.twig');
    }

    #[Route('/api/registrar-colaboradores', name: 'registrar_colaboradores', methods: ['POST'])]
    public function registrarColaboradores(Request $request): JsonResponse
    {
        try {
            $contentType = $request->headers->get('Content-Type');
            $empresaId = null;
            $colaboradores = null;
            $file = null;

            // Manejo de datos JSON o multipart/form-data
            if (str_contains($contentType, 'application/json')) {
                $data = json_decode($request->getContent(), true);
                $empresaId = $data['empresa_id'] ?? null;
                $colaboradores = $data['colaboradores'] ?? null;
            } elseif (str_contains($contentType, 'multipart/form-data')) {
                $empresaId = $request->request->get('empresa_id');
                $file = $request->files->get('file');
            }

            // Validaciones iniciales
            if (!$empresaId) {
                return $this->json(['status' => 'error', 'message' => 'El ID de la empresa es requerido.'], JsonResponse::HTTP_BAD_REQUEST);
            }

            if (!$file && !$colaboradores) {
                return $this->json(['status' => 'error', 'message' => 'Debe enviar un archivo Excel o un JSON con los colaboradores.'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $colaboradoresData = [];

            // Procesar archivo Excel si se proporciona
            if ($file) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
                $sheet = $spreadsheet->getActiveSheet();

                foreach ($sheet->getRowIterator(2) as $row) {
                    $cells = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $cells[] = $cell->getValue();
                    }

                    // Extraer ID de Puesto, Sede y Rol desde el formato dado
                    $puesto = explode(' - ', $cells[0]);
                    $sede = explode(' - ', $cells[1]);
                    $rol = explode(' - ', $cells[9]);

                    // Procesar fecha de nacimiento
                    $cellCoordinate = 'G' . $row->getRowIndex(); // Columna G para fecha de nacimiento
                    $fechaNacimiento = \PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($sheet->getCell($cellCoordinate))
                        ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cells[6])->format('Y-m-d')
                        : $cells[6];

                    $colaboradoresData[] = [
                        'puesto_id' => (int) trim($puesto[1] ?? 0), // Asegurar entero
                        'sede_id' => (int) trim($sede[1] ?? 0), // Asegurar entero
                        'roles' => [trim($rol[1] ?? null)],
                        'col_nombreusuario' => $cells[2],
                        'col_nombres' => $cells[3],
                        'col_apellidos' => $cells[4],
                        'col_dninit' => $cells[5],
                        'col_fechainacimiento' => $fechaNacimiento,
                        'col_correoelecronico' => $cells[7],
                        'password' => $cells[8],
                    ];
                }
            }

            // Registrar colaboradores
            $resultados = $this->colaboradorFunction->registrarColaboradores($empresaId, $colaboradores ?? $colaboradoresData);

            return $this->json(['status' => 'success', 'data' => $resultados], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/descargar-plantilla-colaboradores/{empresaId}', name: 'descargar_plantilla_colaboradores', methods: ['GET'])]
    public function descargarPlantillaColaboradores(int $empresaId): Response
    {
        try {
            $areas = $this->areaFunction->obtenerAreas($empresaId);
            $sedes = $this->sedeFunction->obtenerSedes($empresaId);
            $roles = [
                'Super Administrador - ROLE_SUPERADMIN',
                'Administrador - ROLE_ADMIN',
                'Colaborador - ROLE_COLABORADOR'
            ];

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'Puesto');
            $sheet->setCellValue('B1', 'Sede');
            $sheet->setCellValue('C1', 'Nombre de Usuario');
            $sheet->setCellValue('D1', 'Nombres');
            $sheet->setCellValue('E1', 'Apellidos');
            $sheet->setCellValue('F1', 'DNI/NIT');
            $sheet->setCellValue('G1', 'Fecha de Nacimiento');
            $sheet->setCellValue('H1', 'Correo Electrónico');
            $sheet->setCellValue('I1', 'Contraseña');
            $sheet->setCellValue('J1', 'Roles');

            $puestoList = [];
            foreach ($areas as $area) {
                if (strtolower($area['ara_nombre']) !== 'sistema') {
                    $puestoList[] = "=== Área: {$area['ara_nombre']} ===";
                    foreach ($area['puestos'] as $puesto) {
                        if (strtolower($puesto['pst_nombre']) !== 'sistema') {
                            $puestoList[] = "{$puesto['pst_nombre']} - {$puesto['pst_id']}";
                        }
                    }
                }
            }

            $sedeList = array_map(fn($sede) => "{$sede['sed_nombre']} - {$sede['sed_id']}", $sedes);

            $sheet->setCellValue('L1', 'Puestos Disponibles');
            $row = 2;
            foreach ($puestoList as $puesto) {
                $sheet->setCellValue("L$row", $puesto);
                $row++;
            }

            $sheet->setCellValue('M1', 'Sedes Disponibles');
            $row = 2;
            foreach ($sedeList as $sede) {
                $sheet->setCellValue("M$row", $sede);
                $row++;
            }

            $sheet->setCellValue('N1', 'Roles Disponibles');
            $row = 2;
            foreach ($roles as $role) {
                $sheet->setCellValue("N$row", $role);
                $row++;
            }

            $this->addExcelDropdown($sheet, 'A2:A100', $puestoList);
            $this->addExcelDropdown($sheet, 'B2:B100', $sedeList);
            $this->addExcelDropdown($sheet, 'J2:J100', $roles);

            foreach (range('A', 'N') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $tempFile = tempnam(sys_get_temp_dir(), 'plantilla_colaboradores');
            $writer->save($tempFile);

            return new BinaryFileResponse($tempFile, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment;filename="plantilla_colaboradores.xlsx"',
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    private function addExcelDropdown($sheet, string $range, array $values): void
    {
        if (!empty($values)) {
            $validation = $sheet->getDataValidation($range);
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . implode(',', $values) . '"');
        }
    }
  
    #[Route('/api/colaboradores', name: 'obtener_colaboradores', methods: ['POST'])]
    public function obtenerColaboradores(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $empresaId = $data['empresa_id'] ?? null;
            $colaboradorId = $data['colaborador_id'] ?? null;

            $colaboradores = $this->colaboradorFunction->obtenerColaborador($empresaId, $colaboradorId);

            return $this->json([
                'status' => 'success',
                'data' => $colaboradores,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/colaboradores/editar', name: 'modificar_colaborador', methods: ['PUT'])]
    public function modificarColaborador(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $colaboradorId = $data['colaborador_id'];

            if (!$colaboradorId) {
                throw new \Exception('ID del colaborador es requerido.');
            }

            $colaborador = $this->colaboradorFunction->modificarColaborador($colaboradorId, $data);

            return $this->json([
                'status' => 'success',
                'data' => $colaborador,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/colaboradores/eliminar', name: 'eliminar_colaborador', methods: ['DELETE'])]
    public function eliminarColaborador(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $colaboradorId = $data['colaborador_id'];

            if (!$colaboradorId) {
                throw new \Exception('ID del colaborador es requerido.');
            }

            $resultado = $this->colaboradorFunction->eliminarColaborador($colaboradorId);

            return $this->json([
                'status' => 'success',
                'message' => $resultado['message'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
