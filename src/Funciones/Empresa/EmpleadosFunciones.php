<?php

namespace App\Funciones\Empresa;

use App\Entity\Colaborador;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PDOException;
use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
            'obligatoria' => true,
            'tipo' => 'array'
        ],
        'Correo' => [
            'traduccion' => 'col_correoelectronico',
            'obligatoria' => false
        ],
        'Usuario' => [
            'traduccion' => 'col_nombreusuario',
            'obligatoria' => true,
            'tipo' => 'string'
        ],
        'Contraseña' => [
            'traduccion' => 'password',
            'obligatoria' => true,
            'tipo' => 'string'
        ],
        'Repetir contraseña' => [
            'traduccion' => 'password',
            'obligatoria' => true,
            'tipo' => 'string'
        ]
    ];

    public function __construct(private SluggerInterface $slugger, private EntityManagerInterface $entityManagerInterface, private UserPasswordHasherInterface $userPasswordHasherInterface){ }

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
                                if (array_key_exists($hoja[$condiciones['columnas']['nombres']][$marca], $this->traductor)){
                                    if ($this->traductor[$hoja[$condiciones['columnas']['nombres']][$marca]]['obligatoria']) {
                                        # Validemos tipo de dato
                                        switch ($this->traductor[$hoja[$condiciones['columnas']['nombres']][$marca]]['tipo']) {
                                            case 'array':
                                                $json_string_cleaned = ($campo !== null) ? str_replace(['[', ']', '“', '”'], '', $campo) : null;
                                                $campo_array = ($json_string_cleaned !== null) ? explode(', ', $json_string_cleaned) : null;
                                                if ($this->traductor[$hoja[$condiciones['columnas']['nombres']][$marca]]['tipo'] !== gettype($campo_array)){
                                                    array_push($reporte['errores'], [
                                                        'tipo' => 'campo_tipodedatoerroneo',
                                                        'detalle' => [
                                                            'ubicacion' => [
                                                                'hoja' => $hoja,
                                                                'bloque' => $bloque,
                                                                'fila' => $fila
                                                            ],
                                                            'key' => $marca, 
                                                            'value' => $campo_array
                                                        ]
                                                    ]);
                                                }
                                                break;
                                            default:
                                                if ($this->traductor[$hoja[$condiciones['columnas']['nombres']][$marca]]['tipo'] !== gettype($campo)){
                                                    array_push($reporte['errores'], [
                                                        'tipo' => 'campo_tipodedatoerroneo',
                                                        'detalle' => [
                                                            'ubicacion' => [
                                                                'hoja' => $hoja,
                                                                'bloque' => $bloque,
                                                                'fila' => $fila
                                                            ],
                                                            'key' => $marca, 
                                                            'value' => $campo
                                                        ]
                                                    ]);
                                                }
                                                break;
                                        }

                                        # Validaciones especiales
                                        switch ($hoja[$condiciones['columnas']['nombres']][$marca]) {
                                            case 'Contraseña':
                                                if($campo !== $fila[$marca + 1]){
                                                    array_push($reporte['errores'], [
                                                        'tipo' => 'campo_contrasenasnocoinciden',
                                                        'detalle' => [
                                                            'ubicacion' => [
                                                                'hoja' => $hoja,
                                                                'bloque' => $bloque,
                                                                'fila' => $fila
                                                            ],
                                                            'key' => $marca, 
                                                            'value' => $campo
                                                        ]
                                                    ]);
                                                }
                                                break;
                                            default:
                                                break;
                                        }
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

        if (!empty($reporte['errores'])){
            $reporte['estado'] = false;
        }

        return $reporte;
    }

    public function numeroCamposObligatorios(): int
    {
        $obligatorios = 0;
        foreach ($this->traductor as $valor) {
            if($valor['obligatoria']){
                $obligatorios++;
            }
        }
        return $obligatorios;
    }

    # Registrar empleados (Empresa -> Empleados -> Registar)
    public function registro(array $colorador): string
    {
        $estado = 'Ok';
        $aux_colaborador = new Colaborador;
        $aux_colaborador->setColNombres($colorador['col_nombres'] ?? null);
        $aux_colaborador->setColApellidos($colorador['col_apellidos'] ?? null);
        $aux_colaborador->setColDninit($colorador['col_dninit'] ?? null);
        $aux_colaborador->setColFechanacimiento($colorador['col_fechanacimiento'] ?? null);
        $aux_colaborador->setColPuesto($colorador['col_puesto'] ?? null);
        $aux_colaborador->setColArea($colorador['col_area'] ?? null);
        $aux_colaborador->setColCorreoelectronico($colorador['col_correoelectronico'] ?? null);
        $aux_colaborador->setRoles($colorador['roles']);
        $aux_colaborador->setColNombreusuario($colorador['col_nombreusuario']);
        $aux_colaborador->setColEliminado($colorador['col_eliminado'] ?? null);
        $aux_colaborador->setColEmpresa($colorador['col_empresa_id'] ?? null);
        $aux_colaborador->setColGrupo($colorador['col_grupo_id'] ?? null);
        $aux_colaborador->setPassword($this->userPasswordHasherInterface->hashPassword($aux_colaborador, $colorador['password']));

        try {
            $this->entityManagerInterface->persist($aux_colaborador);
            $this->entityManagerInterface->flush();
        } catch (PDOException $e) {
            $estado = 'Error: '. $e; 
        }

        return $estado;
    }

    # Registrar empleados array (Empresa -> Empleados -> Resgistro masivo)
    public function registroMasivo(array $colaboradores): array
    {
        $estado = 'Ok';
        $errores = [];

        foreach ($colaboradores as $colaborador) {
           array_push($errores, $this->registro($colaborador));
        }

        if(count($errores) > 0){
            $estado = 'Errores encontrados: ' . count($errores);
        }

        return [
            'estado' => $estado,
            'errores' => $errores
        ];
    }

    # Listado Colaboradores
    public function listadoColaboradores(array $datos): array
    {
        $colaboradores = [];

        $condiciones = [
            'hoja' => 'Empleados',
            'columnas' => [
                'nombres' => 'columnNames',
                'valores' => 'columnValues'
            ]
        ];

        # Validemos que sea correcto
        $validar_datos = $this->validar($datos);
        if(!$validar_datos['estado']){
            # Retornar array de datos modificado (Corregido).
            return $validar_datos;
        } else {
            foreach ($datos[$condiciones['hoja']][$condiciones['columnas']['valores']] as $fila) {
                $col = [];
                foreach ($fila as $key => $campo) {
                    # Por tipo de dato
                    switch ($this->traductor[$datos[$condiciones['hoja']][$condiciones['columnas']['nombres']][$key]]['traduccion']) {
                        case 'roles':
                            $json_string_cleaned = ($campo !== null) ? str_replace(['[', ']', '“', '”'], '', $campo) : null;
                            $campo_array = ($json_string_cleaned !== null) ? explode(', ', $json_string_cleaned) : null;
                            $col[$this->traductor[$datos[$condiciones['hoja']][$condiciones['columnas']['nombres']][$key]]['traduccion']] = $campo_array;
                            break;

                        case 'col_fechanacimiento':
                            $fecha_dt = DateTime::createFromFormat('Y-m-d', $campo);
                            $col[$this->traductor[$datos[$condiciones['hoja']][$condiciones['columnas']['nombres']][$key]]['traduccion']] = $fecha_dt;
                            break;
                        
                        default:
                            $col[$this->traductor[$datos[$condiciones['hoja']][$condiciones['columnas']['nombres']][$key]]['traduccion']] = $campo;
                            break;
                    }
                }
                array_push($colaboradores, $col);
            }
        }
        return $colaboradores;
    }
}