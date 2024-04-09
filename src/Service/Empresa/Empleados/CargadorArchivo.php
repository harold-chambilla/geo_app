<?php

namespace App\Service\Empresa\Empleados;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class CargadorArchivo
{
    public function __construct(private string $targetDirectory, private SluggerInterface $slugger){ }

    public function cargar(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // Si falla la carga
        }

        return $fileName; 
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}