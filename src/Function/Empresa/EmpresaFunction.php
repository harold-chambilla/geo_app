<?php

namespace App\Function\Empresa;

use App\Entity\Area;
use App\Entity\Empresa;
use App\Entity\Colaborador;
use App\Entity\Grupo;
use App\Entity\ConfiguracionAsistencia;
use App\Entity\Sede;
use App\Entity\Puesto;
use Doctrine\ORM\EntityManagerInterface;

class EmpresaFunction
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Función para crear una empresa con colaborador, grupo y configuración de asistencia por defecto
    public function crearEmpresa(array $empresaData): array
    {
        // Verificar si ya existe una empresa con el mismo RUC
        $empresaExistente = $this->entityManager->getRepository(Empresa::class)->findOneBy([
            'emp_ruc' => $empresaData['emp_ruc'],
            'emp_eliminado' => false, // Verificamos solo empresas no eliminadas
        ]);

        if ($empresaExistente) {
            throw new \Exception('ya existe una empresa con el mismo ruc');
        }

        // Crear entidad Empresa
        $empresa = new Empresa();
        $empresa->setEmpRuc($empresaData['emp_ruc']);
        $empresa->setEmpNombreEmpresa($empresaData['emp_nombre']);
        $empresa->setEmpIndustria($empresaData['emp_industria']);
        $empresa->setEmpTelefono($empresaData['emp_telefono']);
        $empresa->setEmpCantidadEmpleados(1); // Iniciamos con 1 empleado por defecto
        $empresa->setEmpEliminado(false);

        $this->entityManager->persist($empresa);

        // Crear grupo por defecto para la empresa
        $grupo = new Grupo();
        $grupo->setGrpNombre('general');
        $grupo->setGrpDescripcion('grupo creado por defecto para la empresa');
        $grupo->setGrpEliminado(false);
        $grupo->setEmpresa($empresa);

        $this->entityManager->persist($grupo);

        // Crear colaborador por defecto (Administrador de la empresa)
        $colaborador = new Colaborador();
        $colaborador->setColNombreUsuario($empresaData['superadmin_username']);
        $colaborador->setColNombres($empresaData['superadmin_nombres']);
        $colaborador->setColApellidos($empresaData['superadmin_apellidos']);
        $colaborador->setColDninit($empresaData['superadmin_dni']);
        $colaborador->setColFechainacimiento(new \DateTime($empresaData['superadmin_fecha_nacimiento']));
        $colaborador->setColCorreoelecronico($empresaData['superadmin_email']);
        $colaborador->setPassword(password_hash($empresaData['superadmin_password'], PASSWORD_BCRYPT)); // Contraseña cifrada
        $colaborador->setRoles(['ROLE_SUPERADMIN']); // El rol por defecto será administrador
        $colaborador->setColEliminado(false);
        $colaborador->setGrupo($grupo); // Relacionar con el grupo

        $this->entityManager->persist($colaborador); 

        // Crear sede por defecto para la empresa
        $sede = new Sede();
        $sede->setSedNombre('principal');
        $sede->setSedPais($empresaData['sede_pais']);
        $sede->setSedDireccion($empresaData['sede_direccion']);
        $sede->setSedUbicacion($empresaData['sede_ubicacion']);
        $sede->setSedEliminado(false);

        $this->entityManager->persist($sede);

        // Crear área por defecto para el puesto
        $area = new Area();
        $area->setAraNombre('sistema');
        $area->setAraEliminado(false);

        $this->entityManager->persist($area);

        // Crear puesto por defecto para el colaborador
        $puesto = new Puesto();
        $puesto->setPstNombre('superadministrador');
        $puesto->setPstEliminado(false);
        $puesto->setArea($area); // Definimos el área según la relación

        $this->entityManager->persist($puesto);

        // Crear configuración de asistencia por defecto
        $configuracionAsistencia = new ConfiguracionAsistencia();
        $configuracionAsistencia->setCasTiempoFaltaHoras(8); // Valor por defecto para ausencias
        $configuracionAsistencia->setCasToleranciaIngresoMinutos(15); // Minutos de tolerancia por defecto
        $configuracionAsistencia->setCasPermitirFoto(true); // Permitir toma de fotos en asistencia
        $configuracionAsistencia->setCasHorasextras(true);
        $configuracionAsistencia->setCasFaltasTardanzas(true); // Cantidad de faltas/tardanzas permitidas
        $configuracionAsistencia->setCasPermisos(true); // Número de permisos permitidos
        $configuracionAsistencia->setCasVacaciones(true); // Días de vacaciones por defecto
        $configuracionAsistencia->setCasMarcacion(true); // Activar marcación de asistencia
        $configuracionAsistencia->setCasModalidad('["MOD_PRESENCIAL"]'); // Modalidad por defecto
        $configuracionAsistencia->setCasArea(true);
        $configuracionAsistencia->setCasPuesto(true);
        $configuracionAsistencia->setCasPredhorario(true);
        $configuracionAsistencia->setCasEliminado(false);
        $configuracionAsistencia->setCasEstado('sistema');
        $configuracionAsistencia->setGrupo($grupo); // Relacionar con el grupo por defecto
        $configuracionAsistencia->setSede($sede); // Relacionar con la sede
        $configuracionAsistencia->setPuesto($puesto); // Relacionar con el puesto

        $this->entityManager->persist($configuracionAsistencia);

        // Guardar cambios en la base de datos
        $this->entityManager->flush();

        return [
            'empresa_id' => $empresa->getId(),
            'emp_ruc' => $empresa->getEmpRuc(),
            'emp_nombre' => $empresa->getEmpNombreEmpresa(),
            'superadmin' => [
                'col_nombres' => $colaborador->getColNombres(),
                'col_apellidos' => $colaborador->getColApellidos(),
                'col_email' => $colaborador->getColCorreoelecronico(),
            ],
            'sede' => [
                'sed_nombre' => $sede->getSedNombre(),
                'sed_direccion' => $sede->getSedDireccion(),
            ],
            'area' => $area->getAraNombre(),
            'puesto' => $puesto->getPstNombre(),
            'config_asistencia' => $configuracionAsistencia->getCasEstado(),
        ];
    }

    // Función para editar los datos de una empresa
    public function editarEmpresa(int $empresaId, array $nuevosDatos): array
    {
        $empresa = $this->entityManager->getRepository(Empresa::class)->find($empresaId);
        if (!$empresa) {
            throw new \Exception('empresa no encontrada');
        }

        $empresa->setEmpRuc($nuevosDatos['emp_ruc'] ?? $empresa->getEmpRuc());
        $empresa->setEmpNombreEmpresa($nuevosDatos['emp_nombre'] ?? $empresa->getEmpNombreEmpresa());
        $empresa->setEmpIndustria($nuevosDatos['emp_industria'] ?? $empresa->getEmpIndustria());
        $empresa->setEmpTelefono($nuevosDatos['emp_telefono'] ?? $empresa->getEmpTelefono());
        $empresa->setEmpCantidadEmpleados($nuevosDatos['emp_cantidad_empleados'] ?? $empresa->getEmpCantidadEmpleados());

        // Guardar los cambios
        $this->entityManager->flush();

        return [
            'empresa_id' => $empresa->getId(),
            'emp_ruc' => $empresa->getEmpRuc(),
            'emp_nombre' => $empresa->getEmpNombreEmpresa(),
            'emp_telefono' => $empresa->getEmpTelefono(),
            'emp_industria' => $empresa->getEmpIndustria(),
            'emp_cantidad_empleados' => $empresa->getEmpCantidadEmpleados(),
        ];
    }

    // Función para eliminar una empresa (cambio de estado lógico)
    public function borrarEmpresa(int $empresaId): void
    {
        $empresa = $this->entityManager->getRepository(Empresa::class)->find($empresaId);
        if (!$empresa) {
            throw new \Exception('empresa no encontrada');
        }

        $empresa->setEmpEliminado(true);

        // Si se requiere, también se pueden marcar otros elementos relacionados como eliminados
        $grupos = $empresa->getGrupos();
        foreach ($grupos as $grupo) {
            $grupo->setGrpEliminado(true);
            $confs = $grupo->getConfiguracionAsistencias();
            foreach ($confs as $conf) {
                $conf->setCasEliminado(true);
                $conf->getSede()->setSedEliminado(true);
                $conf->getPuesto()->setPstEliminado(true);
                $conf->getPuesto()->getArea()->setAraEliminado(true);
            }
            $colaboradores = $grupo->getColaboradors();
            foreach ($colaboradores as $colaborador) {
                $colaborador->setColEliminado(true);
                $horarios = $colaborador->getHorarioTrabajos();
                foreach ($horarios as $horario) {
                    $horario->setHotEliminado(true);
                }
                $asistencias = $colaborador->getAsistencias();
                foreach ($asistencias as $asistencia) {
                    $asistencia->setAsiEliminado(true);
                }
                $permisos = $colaborador->getPermisos();
                foreach ($permisos as $permiso) {
                    $permiso->setPmsEliminado(true);
                    $permiso->getMotivo()->setMtvEliminado(true);
                }
            }
        }

        // Guardar los cambios
        $this->entityManager->flush();
    }

    public function obtenerEmpresaConRelaciones(int $empresaId): array
    {
        // Obtener la empresa por ID
        $empresa = $this->entityManager->getRepository(Empresa::class)->find($empresaId);

        if (!$empresa) {
            throw new \Exception('empresa no encontrada');
        }

        // Array para contener la estructura completa de la empresa
        $empresaData = [
            'emp_ruc' => $empresa->getEmpRuc(),
            'emp_nombre' => $empresa->getEmpNombreEmpresa(),
            'emp_industria' => $empresa->getEmpIndustria(),
            'emp_telefono' => $empresa->getEmpTelefono(),
            'emp_cantidad_empleados' => $empresa->getEmpCantidadEmpleados(),
            'emp_eliminado' => $empresa->getEmpEliminado(),
            'grupos' => [],
        ];

        // Obtener todos los grupos asociados a la empresa
        foreach ($empresa->getGrupos() as $grupo) {
            $grupoData = [
                'grp_nombre' => $grupo->getGrpNombre(),
                'grp_descripcion' => $grupo->getGrpDescripcion(),
                'grp_eliminado' => $grupo->getGrpEliminado(),
                'colaboradores' => [],
                'configuraciones_asistencia' => [],
            ];

            // Obtener todos los colaboradores asociados al grupo
            foreach ($grupo->getColaboradors() as $colaborador) {
                $colaboradorData = [
                    'col_nombres' => $colaborador->getColNombres(),
                    'col_apellidos' => $colaborador->getColApellidos(),
                    'col_dni' => $colaborador->getColDni(),
                    'col_correo_electronico' => $colaborador->getColCorreoElectronico(),
                    'col_fecha_nacimiento' => $colaborador->getColFechaNacimiento()->format('Y-m-d'),
                    'col_eliminado' => $colaborador->getColEliminado(),
                    'horarios_trabajo' => [],
                    'asistencias' => [],
                    'permisos' => [],
                ];

                // Obtener los horarios de trabajo del colaborador
                foreach ($colaborador->getHorarioTrabajos() as $horarioTrabajo) {
                    $colaboradorData['horarios_trabajo'][] = [
                        'hot_dia_semana' => $horarioTrabajo->getHotDiasemana(),
                        'hot_hora_entrada' => $horarioTrabajo->getHotHoraEntrada()->format('H:i'),
                        'hot_hora_salida' => $horarioTrabajo->getHotHoraSalida()->format('H:i'),
                    ];
                }

                // Obtener las asistencias del colaborador
                foreach ($colaborador->getAsistencias() as $asistencia) {
                    $colaboradorData['asistencias'][] = [
                        'asi_fecha' => $asistencia->getAsiFecharegistro()->format('Y-m-d'),
                        'asi_hora_entrada' => $asistencia->getAsiHoraentrada()->format('H:i'),
                        'asi_hora_salida' => $asistencia->getAsiHorasalida()->format('H:i'),
                        'asi_eliminado' => $asistencia->getAsiEliminado(),
                    ];
                }

                // Obtener los permisos del colaborador
                foreach ($colaborador->getPermisos() as $permiso) {
                    $colaboradorData['permisos'][] = [
                        'pms_estado' => $permiso->getPmsEstado(),
                        'pms_motivo' => $permiso->getMotivo()->getMtvNombre(),
                        'pms_eliminado' => $permiso->getPmsEliminado(),
                    ];
                }

                $grupoData['colaboradores'][] = $colaboradorData;
            }

            // Obtener las configuraciones de asistencia asociadas al grupo
            foreach ($grupo->getConfiguracionAsistencias() as $configuracion) {
                $configuracionData = [
                    'cas_tiempo_falta_horas' => $configuracion->getCasTiempoFaltaHoras(),
                    'cas_tolerancia_ingreso_minutos' => $configuracion->getCasToleranciaIngresoMinutos(),
                    'cas_vacaciones' => $configuracion->getCasVacaciones(),
                    'cas_estado' => $configuracion->getCasEstado(),
                    'cas_eliminado' => $configuracion->getCasEliminado(),
                    'sede' => [
                        'sed_nombre' => $configuracion->getSede()->getSedNombre(),
                        'sed_direccion' => $configuracion->getSede()->getSedDireccion(),
                        'sed_ubicacion' => $configuracion->getSede()->getSedUbicacion(),
                        'sed_eliminado' => $configuracion->getSede()->getSedEliminado(),
                    ],
                    'puesto' => [
                        'pst_nombre' => $configuracion->getPuesto()->getPstNombre(),
                        'pst_eliminado' => $configuracion->getPuesto()->getPstEliminado(),
                        'area' => [
                            'ara_nombre' => $configuracion->getPuesto()->getArea()->getAraNombre(),
                            'ara_eliminado' => $configuracion->getPuesto()->getArea()->getAraEliminado(),
                        ],
                    ],
                ];

                $grupoData['configuraciones_asistencia'][] = $configuracionData;
            }

            $empresaData['grupos'][] = $grupoData;
        }

        return $empresaData;
    }
}
