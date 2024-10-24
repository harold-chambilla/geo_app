<?php

namespace App\Function\Empresa;

use App\Entity\Motivo;
use App\Entity\Permiso;
use App\Entity\Empresa;
use App\Entity\Colaborador;
use App\Entity\Grupo;
use Doctrine\ORM\EntityManagerInterface;

class MotivoFunction
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Función para registrar un nuevo motivo y luego crear un permiso con estado "Sistema"
    public function registrarMotivo(int $empresaId, array $motivoData): array
    {
        // Buscar la empresa por su ID
        $empresa = $this->entityManager->getRepository(Empresa::class)->find($empresaId);
        if (!$empresa) {
            throw new \Exception('empresa no encontrada');
        }

        // Buscar el grupo "General" de la empresa
        $grupo = $this->entityManager->getRepository(Grupo::class)->findOneBy([
            'empresa' => $empresa,
            'grp_nombre' => 'general',
        ]);
        if (!$grupo) {
            throw new \Exception('grupo "general" no encontrado en esta empresa');
        }

        // Verificar si ya existe un motivo con el mismo nombre
        $motivoExistente = $this->entityManager->getRepository(Motivo::class)->findOneBy([
            'mtv_nombre' => $motivoData['mtv_nombre'],
            'mtv_eliminado' => false,
        ]);

        if ($motivoExistente) {
            throw new \Exception('ya existe un motivo con el mismo nombre');
        }

        // Crear el nuevo motivo
        $motivo = new Motivo();
        $motivo->setMtvNombre($motivoData['mtv_nombre']);
        $motivo->setMtvEliminado(false);

        $this->entityManager->persist($motivo);

        // Guardar el nuevo motivo en la base de datos
        $this->entityManager->flush();

        // Buscar al colaborador con rol Superadministrador
        $colaboradorSuperadmin = $this->entityManager->getRepository(Colaborador::class)->createQueryBuilder('c')
            ->where('c.roles LIKE :role')
            ->andWhere('c.colEliminado = false')
            ->andWhere('c.empresa = :empresa')
            ->setParameter('role', '%ROLE_SUPERADMIN%')
            ->setParameter('empresa', $empresa)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$colaboradorSuperadmin) {
            throw new \Exception('No se encontró un colaborador con rol de superadministrador en la empresa.');
        }

        // Verificar si ya existe un permiso con estado "Sistema" vinculado al colaborador
        $permisoSistema = $this->entityManager->getRepository(Permiso::class)->findOneBy([
            'colaborador' => $colaboradorSuperadmin,
            'pms_estado' => 'sistema',
            'pms_eliminado' => false,
        ]);

        // Si no existe el permiso, lo creamos y lo vinculamos al motivo
        if (!$permisoSistema) {
            $permisoSistema = new Permiso();
            $permisoSistema->setPmsEstado('sistema');
            $permisoSistema->setPmsEliminado(false);
            $permisoSistema->setColaborador($colaboradorSuperadmin);
            $permisoSistema->setMotivo($motivo);

            $this->entityManager->persist($permisoSistema);
        }

        // Guardar el nuevo permiso en la base de datos
        $this->entityManager->flush();

        return [
            'mtv_id' => $motivo->getId(),
            'mtv_nombre' => $motivo->getMtvNombre(),
            'permiso_estado' => $permisoSistema->getPmsEstado(),
        ];
    }

    // Función para obtener todos los motivos vinculados a un permiso y colaborador de la empresa (evitar motivos repetidos)
    public function obtenerMotivos(int $empresaId): array
    {
        // Buscar la empresa por su ID
        $empresa = $this->entityManager->getRepository(Empresa::class)->find($empresaId);
        if (!$empresa) {
            throw new \Exception('Empresa no encontrada');
        }

        // Obtener todos los motivos que estén vinculados a permisos de colaboradores de la empresa
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $motivos = $queryBuilder->select('DISTINCT m')
            ->from(Motivo::class, 'm')
            ->join('m.permisos', 'p') // Relación con permiso
            ->join('p.colaborador', 'c') // Relación con colaborador
            ->join('c.grupo', 'g') // Relación con grupo
            ->where('g.empresa = :empresa')
            ->andWhere('m.mtv_eliminado = false') // Filtrar motivos no eliminados
            ->setParameter('empresa', $empresa)
            ->getQuery()
            ->getResult();

        // Convertir los motivos en un array único (sin repetidos)
        $motivosData = [];
        foreach ($motivos as $motivo) {
            $motivosData[] = [
                'mtv_id' => $motivo->getId(),
                'mtv_nombre' => $motivo->getMtvNombre(),
                'mtv_eliminado' => $motivo->getMtvEliminado(),
            ];
        }

        return $motivosData;
    }

    // Función para editar un motivo
    public function editarMotivo(int $motivoId, array $nuevosDatos): array
    {
        // Buscar el motivo por su ID
        $motivo = $this->entityManager->getRepository(Motivo::class)->find($motivoId);
        if (!$motivo) {
            throw new \Exception('motivo no encontrado');
        }

        // Actualizar los datos del motivo
        $motivo->setMtvNombre($nuevosDatos['mtv_nombre'] ?? $motivo->getMtvNombre());

        // Guardar los cambios en la base de datos
        $this->entityManager->flush();

        return [
            'mtv_id' => $motivo->getId(),
            'mtv_nombre' => $motivo->getMtvNombre(),
        ];
    }

    // Función para eliminar un motivo (cambio de estado lógico)
    public function borrarMotivo(int $motivoId): void
    {
        // Buscar el motivo por su ID
        $motivo = $this->entityManager->getRepository(Motivo::class)->find($motivoId);
        if (!$motivo) {
            throw new \Exception('motivo no encontrado');
        }

        // Marcar el motivo como eliminado
        $motivo->setMtvEliminado(true);

        // Guardar los cambios en la base de datos
        $this->entityManager->flush();
    }
}
