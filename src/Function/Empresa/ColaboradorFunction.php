<?php

namespace App\Function\Empresa;

use App\Entity\Colaborador;
use App\Entity\ConfiguracionAsistencia;
use App\Entity\Empresa;
use App\Entity\Grupo;
use App\Entity\Puesto;
use App\Entity\Sede;
use Doctrine\ORM\EntityManagerInterface;

class ColaboradorFunction
{
    public function __construct(
        private EntityManagerInterface $entityManager   
    ){}

    public function registrarColaborador(int $empresaId, int $puestoId, int $sedeId, array $datosColaborador): array
    {
        // 1. Obtener y validar la empresa
        $empresa = $this->entityManager->getRepository(Empresa::class)->find($empresaId);
        if (!$empresa) {
            throw new \Exception('Empresa no encontrada.');
        }

        // Obtener los primeros 4 números del RUC de la empresa
        $ruc = $empresa->getEmpRuc();
        if (strlen($ruc) < 4) {
            throw new \Exception('El RUC de la empresa no es válido.');
        }
        $rucPrefix = substr($ruc, 0, 4);

        // 2. Obtener y validar el puesto
        $puesto = $this->entityManager->getRepository(Puesto::class)->find($puestoId);
        if (!$puesto) {
            throw new \Exception('Puesto no encontrado.');
        }

        // 3. Obtener y validar la sede
        $sede = $this->entityManager->getRepository(Sede::class)->find($sedeId);
        if (!$sede) {
            throw new \Exception('Sede no encontrada.');
        }

        // 4. Buscar configuración de asistencia con estado "sistema" asociada a la empresa
        $configAsistenciaSistema = $this->entityManager->getRepository(ConfiguracionAsistencia::class)->createQueryBuilder('ca')
            ->join('ca.grupo', 'g')
            ->where('g.empresa = :empresa')
            ->andWhere('ca.cas_estado = :estado')
            ->setParameter('empresa', $empresa)
            ->setParameter('estado', 'sistema')
            ->getQuery()
            ->getOneOrNullResult();

        if (!$configAsistenciaSistema) {
            throw new \Exception('No se encontró una configuración de asistencia con estado "sistema" para la empresa.');
        }

        // Obtener la modalidad desde la configuración de asistencia "sistema"
        $modalidad = $configAsistenciaSistema->getCasModalidad();

        // 5. Buscar configuración de asistencia para el puesto y la sede
        $configAsistencia = $this->entityManager->getRepository(ConfiguracionAsistencia::class)->createQueryBuilder('ca')
            ->join('ca.grupo', 'g')
            ->where('ca.puesto = :puesto')
            ->andWhere('ca.sede = :sede')
            ->andWhere('ca.cas_estado = :estado')
            ->setParameter('puesto', $puesto)
            ->setParameter('sede', $sede)
            ->setParameter('estado', 'puesto') // Solo configuraciones con estado "puesto"
            ->getQuery()
            ->getOneOrNullResult();

        // 6. Determinar el grupo a usar o crear uno nuevo si no existe configuración de asistencia
        if ($configAsistencia) {
            // Configuración encontrada: usar el grupo asociado
            $grupoAsignado = $configAsistencia->getGrupo();
        } else {
            // Crear un nuevo grupo
            $grupoNuevo = new Grupo();
            $grupoNuevo->setGrpNombre($puesto->getPstNombre()); // Nombre del grupo igual al puesto
            $grupoNuevo->setGrpDescripcion('Grupo con la configuración del puesto ' . $puesto->getPstNombre());
            $grupoNuevo->setGrpEliminado(false);
            $grupoNuevo->setEmpresa($empresa);
            $this->entityManager->persist($grupoNuevo);

            // Crear una nueva configuración de asistencia
            $configAsistenciaNueva = new ConfiguracionAsistencia();
            $configAsistenciaNueva->setCasTiempoFaltaHoras($configAsistenciaSistema->getCasTiempoFaltaHoras());
            $configAsistenciaNueva->setCasToleranciaIngresoMinutos($configAsistenciaSistema->getCasToleranciaIngresoMinutos());
            $configAsistenciaNueva->setCasPermitirFoto($configAsistenciaSistema->isCasPermitirFoto());
            $configAsistenciaNueva->setCasHorasextras($configAsistenciaSistema->isCasHorasextras());
            $configAsistenciaNueva->setCasFaltasTardanzas($configAsistenciaSistema->isCasFaltasTardanzas());
            $configAsistenciaNueva->setCasPermisos($configAsistenciaSistema->isCasPermisos());
            $configAsistenciaNueva->setCasVacaciones($configAsistenciaSistema->isCasVacaciones());
            $configAsistenciaNueva->setCasMarcacion($configAsistenciaSistema->isCasMarcacion());
            $configAsistenciaNueva->setCasModalidad($modalidad); // Usar la modalidad del estado "sistema"
            $configAsistenciaNueva->setCasArea($configAsistenciaSistema->isCasArea());
            $configAsistenciaNueva->setCasPuesto($configAsistenciaSistema->isCasPuesto());
            $configAsistenciaNueva->setCasPredhorario($configAsistenciaSistema->isCasPredhorario());
            $configAsistenciaNueva->setCasEliminado(false);
            $configAsistenciaNueva->setCasEstado('puesto');
            $configAsistenciaNueva->setPuesto($puesto);
            $configAsistenciaNueva->setSede($sede);
            $configAsistenciaNueva->setGrupo($grupoNuevo);
            $this->entityManager->persist($configAsistenciaNueva);

            // Asignar el nuevo grupo
            $grupoAsignado = $grupoNuevo;
        }

        // 7. Crear y asignar el nuevo colaborador al grupo
        $nombreUsuario = $rucPrefix . '_' . $datosColaborador['col_nombreusuario'];

        $colaborador = new Colaborador();
        $colaborador->setColNombreusuario($nombreUsuario);
        $colaborador->setColNombres($datosColaborador['col_nombres']);
        $colaborador->setColApellidos($datosColaborador['col_apellidos']);
        $colaborador->setColDninit($datosColaborador['col_dninit']);
        $colaborador->setColFechainacimiento(new \DateTime($datosColaborador['col_fechainacimiento']));
        $colaborador->setColCorreoelecronico($datosColaborador['col_correoelecronico']);
        $colaborador->setPassword(password_hash($datosColaborador['password'], PASSWORD_BCRYPT)); // Contraseña cifrada
        $colaborador->setRoles($datosColaborador['roles']);
        $colaborador->setColEliminado(false);
        $colaborador->setGrupo($grupoAsignado);

        $this->entityManager->persist($colaborador);
        $this->entityManager->flush();

        // 8. Retornar datos del colaborador registrado
        return [
            'colaborador_id' => $colaborador->getId(),
            'nombre_usuario' => $colaborador->getColNombreusuario(),
            'nombres' => $colaborador->getColNombres(),
            'apellidos' => $colaborador->getColApellidos(),
            'grupo' => $grupoAsignado->getGrpNombre(),
        ];
    }

    public function registrarColaboradores(int $empresaId, array $colaboradoresData, bool $isExcel = false): array
    {
        // 1. Obtener y validar la empresa
        $empresa = $this->entityManager->getRepository(Empresa::class)->find($empresaId);
        if (!$empresa) {
            throw new \Exception('Empresa no encontrada.');
        }

        // Obtener los primeros 4 números del RUC de la empresa
        $ruc = $empresa->getEmpRuc();
        if (strlen($ruc) < 4) {
            throw new \Exception('El RUC de la empresa no es válido.');
        }
        $rucPrefix = substr($ruc, 0, 4);

        $colaboradoresRegistrados = [];

        foreach ($colaboradoresData as $datosColaborador) {
            try {
                // Si es Excel, buscar IDs por nombre
                if ($isExcel) {
                    $puesto = $this->entityManager->getRepository(Puesto::class)->findOneBy(['pst_nombre' => $datosColaborador['puesto']]);
                    $sede = $this->entityManager->getRepository(Sede::class)->findOneBy(['sed_nombre' => $datosColaborador['sede']]);
                    if (!$puesto || !$sede) {
                        throw new \Exception('Puesto o sede no encontrados para: ' . $datosColaborador['col_nombreusuario']);
                    }
                    $datosColaborador['puesto_id'] = $puesto->getId();
                    $datosColaborador['sede_id'] = $sede->getId();
                }

                // Registrar colaborador
                $colaborador = $this->registrarColaborador(
                    $empresaId,
                    $datosColaborador['puesto_id'],
                    $datosColaborador['sede_id'],
                    $datosColaborador
                );

                $colaboradoresRegistrados[] = $colaborador;
            } catch (\Exception $e) {
                $colaboradoresRegistrados[] = [
                    'error' => $e->getMessage(),
                    'colaborador' => $datosColaborador,
                ];
            }
        }

        return $colaboradoresRegistrados;
    }    

    public function obtenerColaborador(int $empresaId = null, int $colaboradorId = null): array
    {
        if ($colaboradorId) {
            // Obtener un colaborador específico por ID
            $colaborador = $this->entityManager->getRepository(Colaborador::class)->find($colaboradorId);
            if (!$colaborador) {
                throw new \Exception('Colaborador no encontrado.');
            }

            return [
                'colaborador_id' => $colaborador->getId(),
                'nombre_usuario' => $colaborador->getColNombreusuario(),
                'nombres' => $colaborador->getColNombres(),
                'apellidos' => $colaborador->getColApellidos(),
                'dni' => $colaborador->getColDninit(),
                'fecha_nacimiento' => $colaborador->getColFechainacimiento()->format('Y-m-d'),
                'correo_electronico' => $colaborador->getColCorreoelecronico(),
                'roles' => $colaborador->getRoles(),
                'grupo' => $colaborador->getGrupo() ? $colaborador->getGrupo()->getGrpNombre() : null,
                'puesto' => $colaborador->getGrupo() && $colaborador->getGrupo()->getConfiguracionAsistencias()->first()
                    ? $colaborador->getGrupo()->getConfiguracionAsistencias()->first()->getPuesto()->getPstNombre()
                    : null,
                'sede' => $colaborador->getGrupo() && $colaborador->getGrupo()->getConfiguracionAsistencias()->first()
                    ? $colaborador->getGrupo()->getConfiguracionAsistencias()->first()->getSede()->getSedNombre()
                    : null,
            ];
        } elseif ($empresaId) {
            // Obtener lista de colaboradores por empresa
            $colaboradores = $this->entityManager->getRepository(Colaborador::class)->createQueryBuilder('c')
                ->join('c.grupo', 'g')
                ->where('g.empresa = :empresa')
                ->andWhere('c.col_eliminado = false')
                ->setParameter('empresa', $empresaId)
                ->getQuery()
                ->getResult();

            return array_map(function ($colaborador) {
                return [
                    'colaborador_id' => $colaborador->getId(),
                    'nombre_usuario' => $colaborador->getColNombreusuario(),
                    'nombres' => $colaborador->getColNombres(),
                    'apellidos' => $colaborador->getColApellidos(),
                    'dni' => $colaborador->getColDninit(),
                    'fecha_nacimiento' => $colaborador->getColFechainacimiento()->format('Y-m-d'),
                    'correo_electronico' => $colaborador->getColCorreoelecronico(),
                    'roles' => $colaborador->getRoles(),
                    'grupo' => $colaborador->getGrupo() ? $colaborador->getGrupo()->getGrpNombre() : null,
                    'puesto' => $colaborador->getGrupo() && $colaborador->getGrupo()->getConfiguracionAsistencias()->first()
                        ? $colaborador->getGrupo()->getConfiguracionAsistencias()->first()->getPuesto()->getPstNombre()
                        : null,
                    'sede' => $colaborador->getGrupo() && $colaborador->getGrupo()->getConfiguracionAsistencias()->first()
                        ? $colaborador->getGrupo()->getConfiguracionAsistencias()->first()->getSede()->getSedNombre()
                        : null,
                ];
            }, $colaboradores);
        } else {
            throw new \Exception('Debe proporcionar el ID del colaborador o el ID de la empresa.');
        }
    }

    public function modificarColaborador(int $colaboradorId, array $datosNuevos): array
    {
        // Obtener colaborador
        $colaborador = $this->entityManager->getRepository(Colaborador::class)->find($colaboradorId);
        if (!$colaborador) {
            throw new \Exception('Colaborador no encontrado.');
        }

        // Obtener y validar la empresa
        $empresa = $colaborador->getGrupo()->getEmpresa();
        if (!$empresa) {
            throw new \Exception('Empresa no encontrada.');
        }

        // Obtener los primeros 4 números del RUC de la empresa
        $ruc = $empresa->getEmpRuc();
        if (strlen($ruc) < 4) {
            throw new \Exception('El RUC de la empresa no es válido.');
        }
        $rucPrefix = substr($ruc, 0, 4);


        // Actualizar campos del colaborador
        if (isset($datosNuevos['col_nombreusuario'])) {
            $nombreUsuario = $rucPrefix . '_' . $datosNuevos['col_nombreusuario'];
            $colaborador->setColNombreusuario($nombreUsuario);
        }
        if (isset($datosNuevos['col_nombres'])) {
            $colaborador->setColNombres($datosNuevos['col_nombres']);
        }
        if (isset($datosNuevos['col_apellidos'])) {
            $colaborador->setColApellidos($datosNuevos['col_apellidos']);
        }
        if (isset($datosNuevos['col_dninit'])) {
            $colaborador->setColDninit($datosNuevos['col_dninit']);
        }
        if (isset($datosNuevos['col_fechainacimiento'])) {
            $colaborador->setColFechainacimiento(new \DateTime($datosNuevos['col_fechainacimiento']));
        }
        if (isset($datosNuevos['col_correoelecronico'])) {
            $colaborador->setColCorreoelecronico($datosNuevos['col_correoelecronico']);
        }
        if (isset($datosNuevos['roles'])) {
            $colaborador->setRoles($datosNuevos['roles']);
        }
        if (isset($datosNuevos['password'])) {
            $colaborador->setPassword(password_hash($datosNuevos['password'], PASSWORD_BCRYPT)); // Contraseña cifrada
        }

        // Actualizar grupo si el puesto o la sede cambian
        if (isset($datosNuevos['puesto_id']) || isset($datosNuevos['sede_id'])) {
            $puesto = $this->entityManager->getRepository(Puesto::class)->find($datosNuevos['puesto_id']);
            $sede = $this->entityManager->getRepository(Sede::class)->find($datosNuevos['sede_id']);

            if (!$puesto || !$sede) {
                throw new \Exception('Puesto o sede no encontrado.');
            }

            // Buscar configuración de asistencia para el puesto y la sede
            $configAsistencia = $this->entityManager->getRepository(ConfiguracionAsistencia::class)->findOneBy([
                'puesto' => $puesto,
                'sede' => $sede,
                'cas_estado' => 'puesto',
            ]);

            // Obtener configuración de asistencia "sistema" de la empresa
            $configAsistenciaSistema = $this->entityManager->getRepository(ConfiguracionAsistencia::class)->createQueryBuilder('ca')
                ->join('ca.grupo', 'g')
                ->where('g.empresa = :empresa')
                ->andWhere('ca.cas_estado = :estado')
                ->setParameter('empresa', $empresa)
                ->setParameter('estado', 'sistema')
                ->getQuery()
                ->getOneOrNullResult();

            if (!$configAsistenciaSistema) {
                throw new \Exception('No se encontró una configuración de asistencia con estado "sistema" para la empresa.');
            }

            // Determinar el grupo a usar o crear uno nuevo si no existe configuración de asistencia
            if ($configAsistencia) {
                // Configuración encontrada: usar el grupo asociado
                $colaborador->setGrupo($configAsistencia->getGrupo());
            } else {
                // Crear un nuevo grupo
                $grupoNuevo = new Grupo();
                $grupoNuevo->setGrpNombre($puesto->getPstNombre()); // Nombre del grupo igual al puesto
                $grupoNuevo->setGrpDescripcion('Grupo con la configuración del puesto ' . $puesto->getPstNombre());
                $grupoNuevo->setGrpEliminado(false);
                $grupoNuevo->setEmpresa($empresa);
                $this->entityManager->persist($grupoNuevo);

                // Crear una nueva configuración de asistencia
                $configAsistenciaNueva = new ConfiguracionAsistencia();
                $configAsistenciaNueva->setCasTiempoFaltaHoras($configAsistenciaSistema->getCasTiempoFaltaHoras());
                $configAsistenciaNueva->setCasToleranciaIngresoMinutos($configAsistenciaSistema->getCasToleranciaIngresoMinutos());
                $configAsistenciaNueva->setCasPermitirFoto($configAsistenciaSistema->isCasPermitirFoto());
                $configAsistenciaNueva->setCasHorasextras($configAsistenciaSistema->isCasHorasextras());
                $configAsistenciaNueva->setCasFaltasTardanzas($configAsistenciaSistema->isCasFaltasTardanzas());
                $configAsistenciaNueva->setCasPermisos($configAsistenciaSistema->isCasPermisos());
                $configAsistenciaNueva->setCasVacaciones($configAsistenciaSistema->isCasVacaciones());
                $configAsistenciaNueva->setCasMarcacion($configAsistenciaSistema->isCasMarcacion());
                $configAsistenciaNueva->setCasModalidad($configAsistenciaSistema->getCasModalidad());
                $configAsistenciaNueva->setCasArea($configAsistenciaSistema->isCasArea());
                $configAsistenciaNueva->setCasPuesto($configAsistenciaSistema->isCasPuesto());
                $configAsistenciaNueva->setCasPredhorario($configAsistenciaSistema->isCasPredhorario());
                $configAsistenciaNueva->setCasEliminado(false);
                $configAsistenciaNueva->setCasEstado('puesto');
                $configAsistenciaNueva->setPuesto($puesto);
                $configAsistenciaNueva->setSede($sede);
                $configAsistenciaNueva->setGrupo($grupoNuevo);
                $this->entityManager->persist($configAsistenciaNueva);

                // Asignar el nuevo grupo
                $colaborador->setGrupo($grupoNuevo);
            }
        }

        // Guardar cambios
        $this->entityManager->flush();

        return [
            'colaborador_id' => $colaborador->getId(),
            'nombre_usuario' => $colaborador->getColNombreusuario(),
            'nombres' => $colaborador->getColNombres(),
            'apellidos' => $colaborador->getColApellidos(),
            'grupo' => $colaborador->getGrupo()->getGrpNombre(),
        ];
    }

    public function eliminarColaborador(int $colaboradorId): array
    {
        // Buscar el colaborador
        $colaborador = $this->entityManager->getRepository(Colaborador::class)->find($colaboradorId);
        if (!$colaborador) {
            throw new \Exception('Colaborador no encontrado.');
        }

        // Actualizar el nombre de usuario agregando un sufijo de eliminado
        $nombreUsuarioOriginal = $colaborador->getColNombreusuario();
        $randomString = bin2hex(random_bytes(4)); // Generar una cadena aleatoria de 8 caracteres hexadecimales
        $nuevoNombreUsuario = $nombreUsuarioOriginal . '|deleted_' . $randomString;

        // Marcar al colaborador como eliminado
        $colaborador->setColEliminado(true);
        $colaborador->setColNombreusuario($nuevoNombreUsuario);    

        // Guardar cambios
        $this->entityManager->flush();

        return [
            'status' => 'success',
            'message' => 'Colaborador eliminado correctamente',
            'colaborador_id' => $colaborador->getId(),
        ];
    }    
}
