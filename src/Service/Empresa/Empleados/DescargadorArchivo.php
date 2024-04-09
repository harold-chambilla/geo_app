<?php

namespace App\Service\Empresa\Empleados;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DescargadorArchivo
{
    public function __construct(private string $targetDirectory){ }

    public function descargar(string $fileName): BinaryFileResponse
    {
        $filePath = $this->getTargetDirectory(). '/' . $fileName;
        if (!file_exists($filePath)) {
            throw new \RuntimeException('El archivo no existe.');
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}