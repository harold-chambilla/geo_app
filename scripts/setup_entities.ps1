cd "E:\Datos de los usuarios\mhuertasr\Documentos\GitHub\geo.ksperu.com"

Write-Output "emp_ruc`nstring`n`nno`nemp_nombreempresa`nstring`n`nno`nemp_industria`nstring`n`nno`nemp_telefono`nstring`n`nno`nemp_cantidadempleados`ninteger`nno`nemp_eliminado`nboolean`nno`n" | php bin/console make:entity Empresa

Write-Output "sed_nombre`nstring`n`nno`nsed_pais`nstring`n`nno`nsed_direccion`nstring`n`nno`nsed_ubicacion`nstring`n`nno`nsed_eliminado`nboolean`nno`n" | php bin/console make:entity Sede

Write-Output "grp_nombre`nstring`n`nno`ngrp_descripcion`nstring`n`nno`ngrp_eliminado`nboolean`nno`nempresa`nrelation`nEmpresa`nManyToOne`nno`n`n`n`n" | php bin/console make:entity Grupo

Write-Output "ara_nombre`nstring`n`nno`nara_eliminado`nboolean`nno`n" | php bin/console make:entity Area

Write-Output "pst_nombre`nstring`n`nno`npst_eliminado`nboolean`nno`narea`nrelation`nArea`nManyToOne`nno`n`n`n`n" | php bin/console make:entity Puesto

Write-Output "cas_tiempo_falta_horas`ninteger`nno`ncas_tolerancia_ingreso_minutos`ninteger`nno`ncas_permitir_foto`nboolean`nno`ncas_faltas_tardanzas`nboolean`nno`ncas_permisos`nboolean`nno`ncas_vacaciones`nboolean`nno`ncas_marcacion`nboolean`nno`ncas_modalidad`nstring`n`nno`ncas_area`nboolean`nno`ncas_puesto`nboolean`nno`ncas_predhorario`nboolean`nno`ncas_eliminado`nboolean`nno`ncas_estado`nstring`n`nno`ngrupo`nrelation`nGrupo`nManyToOne`nno`n`n`n`nsede`nrelation`nSede`nManyToOne`nno`n`n`n`npuesto`nrelation`nPuesto`nManyToOne`nno`n`n`n`n" | php bin/console make:entity ConfiguracionAsistencia

Write-Output "yes`ncol_nombreusuario`nyes`ncol_nombres`nstring`n`nno`ncol_apellidos`nstring`n`nno`ncol_dninit`nstring`n`nno`ncol_fechainacimiento`ndate`nno`ncol_correoelecronico`nstring`n`nno`ncol_eliminado`nboolean`nno`ngrupo`nrelation`nGrupo`nManyToOne`nno`n`n`n`n" | php bin/console make:user Colaborador

Write-Output "asi_fechaentrada`ndatetime`nno`nasi_fechasalida`ndatetime`nno`nasi_horaentrada`ntime`nno`nasi_horasalida`ntime`nno`nasi_fotoentrada`nstring`n`nno`nasi_fotosalida`nstring`n`nno`nasi_ubicacionentrada`nstring`n`nno`nasi_ubicacionsalida`nstring`n`nno`nasi_estadoentrada`nstring`n`nno`nasi_estadosalida`nstring`n`nno`nasi_notas`nstring`n`nno`nasi_eliminado`nboolean`nno`ncolaborador`nrelation`nColaborador`nManyToOne`nno`n`n`n`n" | php bin/console make:entity Asistencia

Write-Output "mtv_nombre`nstring`n`nno`nmtv_eliminado`nboolean`nno`n" | php bin/console make:entity Motivo

Write-Output "pms_estado`nstring`n`nno`npms_eliminado`nboolean`nno`nmotivo`nrelation`nMotivo`nOneToMany`n`nno`n`ncolaborador`nrelation`nColaborador`nManyToOne`nno`n`n`n`n" | php bin/console make:entity Permiso

Write-Output "hot_diasemana`nstring`n`nno`nhot_fecha`ndate`nno`nhot_horaentrada`ntime`nno`nhot_horasalida`ntime`nno`nhot_tipojornada`nstring`n`nno`nhot_eliminado`nboolean`nno`ncolaborador`nrelation`nColaborador`nManyToOne`nno`n`n`n`n" | php bin/console make:entity HorarioTrabajo

Write-Output "reg_tablaafectada`nstring`n`nno`nreg_campoafectado`nstring`n`nno`nreg_valoranterior`nstring`n`nno`nreg_valornuevo`nstring`n`nno`nreg_fecha`ndatetime`nno`ncolaborador`nrelation`nolaborador`nManyToOne`nno`n`n`n`n" | php bin/console make:entity RegistroCambios

php bin/console make:migration

php bin/console doctrine:migrations:migrate --no-interaction
