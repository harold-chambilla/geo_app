<?php

namespace App\Funciones\Empresa;

use App\Entity\Colaborador;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class EmpleadosFunciones
{
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
}