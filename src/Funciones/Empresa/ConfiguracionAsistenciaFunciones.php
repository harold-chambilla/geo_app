<?php
namespace App\Funciones\Empresa;

use App\Entity\ConfiguracionAsistencia;
use App\Repository\ConfiguracionAsistenciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use PDOException;
use Symfony\Bundle\SecurityBundle\Security;

class ConfiguracionAsistenciaFunciones
{
    public function __construct(
        private EntityManagerInterface $entityManagerInterface,
        private Security $security,
        private ConfiguracionAsistenciaRepository $configuracionAsistenciaRepository
    ){ }

    /**
     * Función para obtener los datos de Tmp. de falta horas, Tolerancia de ingreso y Permitir foto
     *
     * @param int $id El ID de la configuración de asistencia
     * @return array|null Devuelve un arreglo con los datos o null si no se encuentra
     */
    public function obtenerDatosAsistencia(int $id): ?array
    {
        // Buscar la configuración de asistencia por ID
        $configuracion = $this->configuracionAsistenciaRepository->find($id);

        if (!$configuracion) {
            return null; // Retorna null si no encuentra la configuración
        }

        // Retornar los valores de los atributos requeridos
        return [
            'tiempo_falta_horas' => $configuracion->getCasTiempoFaltaHoras(),
            'tolerancia_ingreso_minutos' => $configuracion->getCasToleranciaIngresoMinutos(),
            'permitir_foto' => $configuracion->isCasPermitirFoto(),
        ];
    }

    /**
     * Función para insertar o actualizar los datos en la entidad ConfiguracionAsistencia
     *
     * @param array $datos Datos a insertar (tiempo de falta, tolerancia de ingreso y si permite foto)
     * @return string Estado de la operación ('Ok' o 'Error: ...')
     */
    public function registrarDatosAsistencia(array $datos): string
    {
        $estado = 'Ok';

        // Intentar obtener una configuración existente o crear una nueva
        $configuracion = $this->configuracionAsistenciaRepository->find($datos['id'] ?? null);
        if (!$configuracion) {
            $configuracion = new ConfiguracionAsistencia();
        }

        // Asignar los valores a la entidad
        $configuracion->setCasTiempoFaltaHoras($datos['tiempo_falta_horas'] ?? null);
        $configuracion->setCasToleranciaIngresoMinutos($datos['tolerancia_ingreso_minutos'] ?? null);
        $configuracion->setCasPermitirFoto($datos['permitir_foto'] ?? false);

        try {
            // Guardar los cambios en la base de datos
            $this->entityManagerInterface->persist($configuracion);
            $this->entityManagerInterface->flush();
        } catch (PDOException $e) {
            $estado = 'Error: '. $e->getMessage();
        }

        return $estado;
    }

    /**
     * Función para obtener el estado de las notificaciones activas.
     *
     * @param int $id El ID de la configuración de asistencia
     * @return array|null Devuelve un arreglo con los estados de las notificaciones o null si no se encuentra
     */
    public function obtenerNotificacionesActivas(int $id): ?array
    {
        $configuracion = $this->configuracionAsistenciaRepository->find($id);

        if (!$configuracion) {
            return null;
        }

        return [
            'faltas_tardanzas' => $configuracion->isCasFaltasTardanzas(),
            'permisos' => $configuracion->isCasPermisos(),
            'vacaciones' => $configuracion->isCasVacaciones(),
            'marcacion' => $configuracion->isCasMarcacion()
        ];
    }

    /**
     * Función para actualizar el estado de las notificaciones activas.
     *
     * @param int $id El ID de la configuración de asistencia
     * @param array $datos Datos a insertar (booleanos para los envíos de notificaciones)
     * @return string Estado de la operación ('Ok' o 'Error: ...')
     */
    public function actualizarNotificacionesActivas(int $id, array $datos): string
    {
        $estado = 'Ok';

        $configuracion = $this->configuracionAsistenciaRepository->find($id);
        if (!$configuracion) {
            return 'Error: Configuración de asistencia no encontrada';
        }

        // Asignar valores a la entidad
        $configuracion->setCasFaltasTardanzas($datos['faltas_tardanzas'] ?? false);
        $configuracion->setCasPermisos($datos['permisos'] ?? false);
        $configuracion->setCasVacaciones($datos['vacaciones'] ?? false);
        $configuracion->setCasMarcacion($datos['marcacion'] ?? false);

        try {
            $this->entityManagerInterface->persist($configuracion);
            $this->entityManagerInterface->flush();
        } catch (PDOException $e) {
            $estado = 'Error: ' . $e->getMessage();
        }

        return $estado;
    }

    /**
     * Función para obtener la configuración de asistencia para los campos de área, puesto, modalidad y horario predeterminado.
     *
     * @param int $id El ID de la configuración de asistencia
     * @return array|null Devuelve un arreglo con los datos o null si no se encuentra
     */
    public function obtenerConfiguracionTrabajo(int $id): ?array
    {
        $configuracion = $this->configuracionAsistenciaRepository->find($id);

        if (!$configuracion) {
            return null;
        }

        return [
            'area' => $configuracion->isCasArea(),
            'puesto' => $configuracion->isCasPuesto(),
            'modalidadTrabajo' => $configuracion->getCasModalidad(),
            'predeterminarHorario' => $configuracion->isCasPredhorario()
        ];
    }

    /**
     * Función para actualizar la configuración de trabajo en los campos de área, puesto, modalidad y horario predeterminado.
     *
     * @param int $id El ID de la configuración de asistencia
     * @param array $datos Datos para actualizar (area, puesto, modalidadTrabajo, predeterminarHorario)
     * @return string Estado de la operación ('Ok' o 'Error: ...')
     */
    public function actualizarConfiguracionTrabajo(int $id, array $datos): string
    {
        $estado = 'Ok';

        $configuracion = $this->configuracionAsistenciaRepository->find($id);
        if (!$configuracion) {
            return 'Error: Configuración de asistencia no encontrada';
        }

        // Asignar valores a la entidad
        $configuracion->setCasArea($datos['area'] ?? null);
        $configuracion->setCasPuesto($datos['puesto'] ?? null);
        $configuracion->setCasModalidad($datos['modalidadTrabajo'] ?? null);
        $configuracion->setCasPredhorario($datos['predeterminarHorario'] ?? null);

        try {
            $this->entityManagerInterface->persist($configuracion);
            $this->entityManagerInterface->flush();
        } catch (PDOException $e) {
            $estado = 'Error: ' . $e->getMessage();
        }

        return $estado;
    }
}

