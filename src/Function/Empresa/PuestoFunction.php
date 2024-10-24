<?php

namespace App\Function\Empresa;

use App\Entity\Puesto;
use App\Entity\Area;
use Doctrine\ORM\EntityManagerInterface;

class PuestoFunction
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Función para registrar un puesto en un área existente de la empresa
    public function registrarPuesto(int $areaId, array $puestoData): array
    {
        // Buscar el área por su ID
        $area = $this->entityManager->getRepository(Area::class)->find($areaId);
        if (!$area) {
            throw new \Exception('area no encontrada');
        }

        // Verificar si ya existe un puesto con el mismo nombre en el área
        $puestoExistente = $this->entityManager->getRepository(Puesto::class)->findOneBy([
            'area' => $area,
            'pst_nombre' => $puestoData['pst_nombre'],
            'pst_eliminado' => false, // Solo verificamos los puestos no eliminados
        ]);

        if ($puestoExistente) {
            throw new \Exception('ya existe un puesto con el mismo nombre en esta area');
        }

        // Crear el nuevo puesto
        $puesto = new Puesto();
        $puesto->setPstNombre($puestoData['pst_nombre']);
        $puesto->setPstEliminado(false);
        $puesto->setArea($area);  // Relacionar con el área

        $this->entityManager->persist($puesto);

        // Guardar los cambios en la base de datos
        $this->entityManager->flush();

        return [
            'pst_id' => $puesto->getId(),
            'pst_nombre' => $puesto->getPstNombre(),
            'area' => [
                'ara_id' => $area->getId(),
                'ara_nombre' => $area->getAraNombre(),
            ]
        ];
    }

    // Función para obtener un puesto por su ID
    public function obtenerPuestoPorId(int $puestoId): array
    {
        // Buscar el puesto por su ID
        $puesto = $this->entityManager->getRepository(Puesto::class)->find($puestoId);
        if (!$puesto || $puesto->getPstEliminado()) {
            throw new \Exception('puesto no encontrado o está eliminado');
        }

        // Retornar los datos del puesto
        return [
            'pst_id' => $puesto->getId(),
            'pst_nombre' => $puesto->getPstNombre(),
            'area' => [
                'ara_id' => $puesto->getArea()->getId(),
                'ara_nombre' => $puesto->getArea()->getAraNombre(),
            ]
        ];
    }

    // Función para editar un puesto existente
    public function editarPuesto(int $puestoId, array $nuevosDatos): array
    {
        // Buscar el puesto por su ID
        $puesto = $this->entityManager->getRepository(Puesto::class)->find($puestoId);
        if (!$puesto) {
            throw new \Exception('puesto no encontrado');
        }

        // Actualizar los datos del puesto
        $puesto->setPstNombre($nuevosDatos['pst_nombre'] ?? $puesto->getPstNombre());

        // Guardar los cambios en la base de datos
        $this->entityManager->flush();

        return [
            'pst_id' => $puesto->getId(),
            'pst_nombre' => $puesto->getPstNombre(),
            'area' => [
                'ara_id' => $puesto->getArea()->getId(),
                'ara_nombre' => $puesto->getArea()->getAraNombre(),
            ]
        ];
    }

    // Función para eliminar un puesto (cambio de estado lógico)
    public function borrarPuesto(int $puestoId): void
    {
        // Buscar el puesto por su ID
        $puesto = $this->entityManager->getRepository(Puesto::class)->find($puestoId);
        if (!$puesto) {
            throw new \Exception('puesto no encontrado');
        }

        // Marcar el puesto como eliminado
        $puesto->setPstEliminado(true);

        // Guardar los cambios en la base de datos
        $this->entityManager->flush();
    }
}

