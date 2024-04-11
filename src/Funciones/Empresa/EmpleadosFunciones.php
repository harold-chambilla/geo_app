<?php

namespace App\Funciones\Empresa;

use App\Entity\Colaborador;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpParser\Node\Stmt\Break_;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class EmpleadosFunciones
{
    public array $traductor = [
        'Nombres' => [
            'traduccion' => 'col_nombres',
            'obligatoria' => false
        ],
        'Apellidos' => [
            'traduccion' => 'col_apellidos',
            'obligatoria' => false
        ],
        'DNI' => [
            'traduccion' => 'col_dninit',
            'obligatoria' => false
        ],
        'Fecha de nacimiento' => [
            'traduccion' => 'col_fechanacimiento',
            'obligatoria' => false
        ],
        'Area' => [
            'traduccion' => 'col_area',
            'obligatoria' => false
        ],
        'Puesto' => [
            'traduccion' => 'col_puesto',
            'obligatoria' => false
        ],
        'Rol' => [
            'traduccion' => 'roles',
            'obligatoria' => true
        ],
        'Correo' => [
            'traduccion' => 'col_correoelectronico',
            'obligatoria' => false
        ],
        'Usuario' => [
            'traduccion' => 'col_nombreusuario',
            'obligatoria' => true
        ],
        'Contraseña' => [
            'traduccion' => 'password',
            'obligatoria' => true
        ],
        'Repetir contraseña' => [
            'traduccion' => 'password',
            'obligatoria' => true
        ]
    ];

    public function __construct(private SluggerInterface $slugger){ }

    # Cargar un archivo
    public function cargar(UploadedFile $file, string $uploadDirectory): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($uploadDirectory, $fileName);
        } catch (FileException $e) {
            // Si falla la carga
        }

        return $fileName; 
    }

    # Descargar un archivo
    public function descargar(string $fileName, string $downloadDirectory): BinaryFileResponse
    {
        $filePath = $downloadDirectory. '/' . $fileName;
        if (!file_exists($filePath)) {
            throw new \RuntimeException('El archivo no existe.');
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }

    # Extraer datos de un archivo
    public function extraer(string $fileName, string $containerDirectory): array
    {
        $data = [];
        $filePath = $containerDirectory. '/' . $fileName;
        try {
            $fileloaded = $this->loadFile($filePath);
            foreach($fileloaded->getWorksheetIterator() as $worksheet){
                $worksheetTitle = $worksheet->getTitle();
                $data[$worksheetTitle] = [
                    'columnNames' => [],
                    'columnValues' => [],
                ];
                foreach ($worksheet->getRowIterator() as $row) {
                    $rowIndex = $row->getRowIndex();
                    if ($rowIndex > 2) {
                        $data[$worksheetTitle]['columnValues'][$rowIndex] = [];
                    }
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false); // Loop over all cells, even if it is not set
                    foreach ($cellIterator as $cell) {
                        if ($rowIndex === 1) {
                            $data[$worksheetTitle]['columnNames'][] = $cell->getCalculatedValue();
                        }
                        if ($rowIndex > 1) {
                            $data[$worksheetTitle]['columnValues'][$rowIndex][] = $cell->getCalculatedValue();
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Si falla la lectura y extracción
        }
        return $data;
    }

    public function loadFile($filePath)
    {
        return IOFactory::load($filePath);
    }

    # Analizar y validar que el archivo este estructurado de forma correcta
    public function validar(array $datos): array
    {
        $condiciones = [
            'hoja' => 'Empleados',
            'columnas' => [
                'nombres' => 'columnNames',
                'valores' => 'columnValues'
            ]
        ];

        $reporte = [
            'estado' => true,
            'errores' => []
        ]; 

        foreach ($datos as $clave => $hoja) {
            if ($condiciones['hoja'] === $clave){
                foreach ($hoja as $llave => $bloque) {
                    if($condiciones['columnas']['nombres'] === $llave){
                        foreach ($bloque as $marca => $campo) {
                            if (!array_key_exists($campo, $this->traductor)){
                                array_push($reporte['errores'], [
                                    'tipo' => 'columna_noreconocida',
                                    'detalle' => [
                                        'ubicacion' => [
                                            'hoja' => $hoja,
                                            'bloque' => $bloque
                                        ],
                                        'key' => $marca, 
                                        'value' => $campo
                                    ]
                                ]);
                            }
                        }
                    } elseif ($condiciones['columnas']['valores']  === $llave) {
                        foreach ($bloque as $selector => $fila) {
                            foreach ($fila as $marca => $campo) {
                                # Evaluar los campos no nulos, evaluar el tipo de dato correcto
                                if (array_key_exists($campo, $this->traductor)){
                                    if ($this->traductor[$hoja[$condiciones['columnas']['nombres']][$marca]]['obligatoria']) {
                                        
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                array_push($reporte['errores'], [
                    'tipo' => 'hoja_noreconocida',
                    'detalle' => [
                        'key' => $clave,
                        'value' => $hoja
                    ]
                ]);
            }
        }

        if (empty($reporte['errores'])){
            $reporte['estado'] = false;
        }

        return $reporte;
    }
}