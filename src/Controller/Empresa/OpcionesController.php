<?php 

namespace App\Controller\Empresa;

use App\Entity\Colaborador;
use App\Entity\Empresa;
use App\Entity\Sede;
use App\Funciones\Empresa\AreaFunciones;
use App\Funciones\Empresa\ConfiguracionAsistenciaFunciones;
use App\Funciones\Empresa\EmpresaFunciones;
use App\Funciones\Empresa\PermisoFunciones;
use App\Repository\ColaboradorRepository;
use App\Repository\ConfiguracionAsistenciaRepository;
use App\Repository\EmpresaRepository;
use App\Repository\GrupoRepository;
use App\Repository\SedeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Func;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/empresa/opciones', name: 'app_empresa_opciones_')]
class OpcionesController extends AbstractController 
{	
	public function __construct(
		private ColaboradorRepository $colaboradorRepository, 
        private EmpresaFunciones $empresaFunciones,
        private AreaFunciones $areaFunciones,
        private PermisoFunciones $permisoFunciones,
        private SedeRepository $sedeRepository,
        private Security $security,
        private EmpresaRepository $empresaRepository,
        private GrupoRepository $grupoRepository,
        private ConfiguracionAsistenciaRepository $configuracionAsistenciaRepository,
        private ConfiguracionAsistenciaFunciones $configuracionAsistenciaFunciones
	){}

	#[Route('/', name: 'mostrar')]
	public function mostrar(): Response
	{
		return $this->render('empresa/opciones/index.html.twig');
	}

	#[Route('/api/ruc', name: 'obtener_ruc')]
	public function obteneRuc(): JsonResponse
	{
		if($this->empresaFunciones->obtenerEmpresa()){
			return $this->json($this->empresaFunciones->obtenerEmpresa()->getEmpRuc());
		}
		return $this->json(null, JsonResponse::HTTP_NOT_FOUND);	
	}

	//Obtener razon social
	#[Route('/api/razonsocial', name: 'obtener_razonsocial')]
	public function obtenerRazonSocial(): JsonResponse
	{
		if($this->empresaFunciones->obtenerEmpresa()){
			return $this->json($this->empresaFunciones->obtenerEmpresa()->getEmpNombreempresa());
		}
		return $this->json(null, JsonResponse::HTTP_NOT_FOUND);
	}

	//Obtener sedes y sucursales
	#[Route('/api/sedes', name: 'obtener_sedes')]
	public function obtenerSedes(): JsonResponse
	{	
		if($this->empresaFunciones->obtenerEmpresa()){
			return $this->json($this->empresaFunciones->obtenerEmpresa()->getEmpSedes());
		}
		return $this->json(null, JsonResponse::HTTP_NOT_FOUND);
    }

    #[Route('/api/areas', name: 'obtener_areas')]
	public function obtenerAreas(): JsonResponse
    {	

		if($this->empresaFunciones->obtenerEmpresa()){
			return $this->json($this->areaFunciones->obtenerAreas($this->empresaFunciones->obtenerEmpresa()));
		}
		return $this->json(null, JsonResponse::HTTP_NOT_FOUND);
    }

    #[Route('/api/registrar-areas', name: 'registrar_areas', methods: ['POST'])]
    public function registrarAreas(Request $request): JsonResponse
    {
        // Obtener la empresa del colaborador autenticado
        $empresa = $this->empresaFunciones->obtenerEmpresa();

        // Verificar si la empresa fue encontrada
        if (!$empresa) {
            return new JsonResponse(['error' => 'Empresa no encontrada'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Obtener el contenido JSON del cuerpo de la solicitud
        $areas = json_decode($request->getContent(), true);

        // Verificar si los datos de las áreas son válidos
        if (!$areas || !is_array($areas)) {
            return new JsonResponse(['error' => 'Datos inválidos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Llamar a la función para registrar las áreas y puestos
        $resultado = $this->areaFunciones->registroAreasYPuestos($areas, $empresa);

        // Verificar si la función de registro devuelve 'Ok' o un error
        if ($resultado === 'Ok') {
            return new JsonResponse(['message' => 'Áreas y puestos registrados correctamente'], JsonResponse::HTTP_OK);
        } else {
            return new JsonResponse(['error' => $resultado], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/motivos', name: 'obtener_motivos', methods: ['GET'])]
    public function obtenerMotivos(): JsonResponse
    {
        // Obtener el usuario autenticado
        $usuario = $this->security->getUser();

        // Buscar el colaborador asociado al usuario autenticado
        $colaborador = $this->colaboradorRepository->findOneBy(['col_nombreusuario' => $usuario->getUserIdentifier()]);

        // Verificar si se encontró el colaborador
        if (!$colaborador) {
            return new JsonResponse(['error' => 'Colaborador no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Llamar a la función para obtener los motivos del colaborador
        $motivos = $this->permisoFunciones->obtenerMotivos($colaborador);

        // Retornar los motivos como un JSON
        return new JsonResponse($motivos, JsonResponse::HTTP_OK);
    }

    #[Route('/api/insertar-motivos', name: 'insertar_motivos', methods: ['POST'])]
    public function insertarMotivos(Request $request): JsonResponse
    {
        // Obtener el usuario autenticado
        $usuario = $this->security->getUser();

        // Buscar el colaborador asociado al usuario autenticado
        $colaborador = $this->colaboradorRepository->findOneBy(['col_nombreusuario' => $usuario->getUserIdentifier()]);

        // Verificar si se encontró el colaborador
        if (!$colaborador) {
            return new JsonResponse(['error' => 'Colaborador no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Obtener el contenido JSON de la solicitud
        $data = json_decode($request->getContent(), true);

        // Verificar si se enviaron motivos
        if (!isset($data['motivos']) || !is_array($data['motivos'])) {
            return new JsonResponse(['error' => 'Formato inválido. Se requiere un array de motivos.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Llamar a la función para agregar los motivos
        $resultado = $this->permisoFunciones->agregarMotivos($data['motivos'], $colaborador);

        // Verificar si la función de agregarMotivos devuelve 'Ok' o un error
        if ($resultado === 'Ok') {
            return new JsonResponse(['message' => 'Motivos agregados correctamente'], JsonResponse::HTTP_OK);
        } else {
            return new JsonResponse(['error' => $resultado], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/listar/sedes', name: 'listar_sedes', methods: ['GET'])]
    public function listarSedes(): JsonResponse
    {
        // Obtener el usuario autenticado
        $usuario = $this->getUser();

        if (!$usuario) {
            return new JsonResponse(['error' => 'Colaborador no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Obtener la empresa a la que pertenece el usuario
        $empresa = $usuario->getColEmpresa(); // Suponiendo que el usuario tiene una relación con la empresa

        if (!$empresa) {
            return new JsonResponse(['error' => 'Empresa no encontrada'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Obtener las sedes de la empresa
        $sedes = $this->sedeRepository->findBy(['sed_empresa' => $empresa]);

        if (empty($sedes)) {
            return new JsonResponse(['error' => 'No se encontraron sedes para esta empresa'], JsonResponse::HTTP_NOT_FOUND);
        }
        
        // Radio por defecto para las sedes
        $radioPorDefecto = 300; // Valor en metros

        // Crear una respuesta con la información de las sedes
        $data = [];
        foreach ($sedes as $sede) {
            $data[] = [
                'id' => $sede->getId(),
                'nombre' => $sede->getSedNombre(),
                'direccion' => $sede->getSedDireccion(),
                'latitud' => $sede->getSedUbicacion()[0],
                'longitud' => $sede->getSedUbicacion()[1],
                'radio' => $radioPorDefecto
            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/api/guardar/sedes', name: 'guardar_coordenadas', methods: ['POST'])]
    public function guardarCoordenadas(Request $request, EmpresaRepository $empresaRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $usuario = $this->getUser();

    // Validar que el usuario tenga el rol de administrador
    // if (!in_array('ROLE_ADMIN', $usuario->getRoles())) {
    //     return new JsonResponse(['error' => 'Acceso denegado. Solo los administradores pueden crear sedes.'], JsonResponse::HTTP_FORBIDDEN);
    // }

    // Buscar la empresa asociada al usuario autenticado
    $empresaId = $usuario->getColEmpresa(); // Asumiendo que el usuario tiene una relación con la empresa

   

    $empresa = $empresaRepository->find(['id' => $empresaId]);

    if (!$empresa) {
        return new JsonResponse(['error' => 'Empresa no encontrada para el usuario autenticado.'], JsonResponse::HTTP_NOT_FOUND);
    }

    // Obtener los datos de la solicitud (latitud, longitud, dirección)
    $nombreEmpresa = $request->request->get('nombreEmpresa');
    $latitud = $request->request->get('latitud');
    $longitud = $request->request->get('longitud');
    $direccion = $request->request->get('direccion');
    $pais = $request->request->get('pais');

    // Validar que todos los datos estén presentes
    if (!$latitud || !$longitud || !$direccion) {
        return new JsonResponse(['error' => 'Faltan datos obligatorios.'], JsonResponse::HTTP_BAD_REQUEST);
    }

    // Crear una nueva entidad Sede
    $sede = new Sede();
    $sede->setSedNombre($nombreEmpresa);
    $sede->setSedUbicacion([$latitud, $longitud]);  // Establecer las coordenadas
    $sede->setSedDireccion($direccion);  // Establecer la dirección
    $sede->setSedEmpresa($empresaId); // Asociar la sede a la empresa del usuario autenticado
    $sede->setSedPais($pais);

    // Guardar la nueva sede en la base de datos
    $entityManager->persist($sede);
    $entityManager->flush();

    // Devolver la sede creada
    return new JsonResponse([
        'id' => $sede->getId(),
        // 'ubicacion' => [$latitud, $longitud],
        'nombre' => $sede->getSedNombre(),
        'direccion' => $sede->getSedDireccion(),
        // 'empresaId' => $empresa->getId(),
    ], JsonResponse::HTTP_CREATED);
    }

    // src/Controller/SedeController.php

    #[Route('/api/borrar/sedes/{id}', name: 'borrar_sede', methods: ['DELETE'])]
    public function borrarSede(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        // Buscar la sede por ID
        $sede = $this->sedeRepository->find($id);

        // Verificar si la sede existe
        if (!$sede) {
            return new JsonResponse(['error' => 'Sede no encontrada'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Eliminar la sede
        $entityManager->remove($sede);
        $entityManager->flush();

        // Responder con un mensaje de éxito
        return new JsonResponse(['message' => 'Sede eliminada correctamente'], JsonResponse::HTTP_OK);
    }

    #[Route('/api/obtener-configuracion-asistencia', name: 'obtener_configuracion_asistencia', methods: ['GET'])]
    public function obtenerConfiguracionAsistencia(): JsonResponse
    {
        $grupoPredeterminado = $this->grupoRepository->findOneBy([
            'grp_nombre' => 'Predeterminado'
        ]);

        $id = $this->configuracionAsistenciaRepository->findOneBy([
           'cas_grupo' => $grupoPredeterminado 
        ])->getId();

        // Llamar a la función para obtener los datos de asistencia por ID
        $datos = $this->configuracionAsistenciaFunciones->obtenerDatosAsistencia($id);

        if (!$datos) {
            return $this->json(['error' => 'Configuración no encontrada'], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json($datos, JsonResponse::HTTP_OK);
    }

    #[Route('/api/registrar-configuracion-asistencia', name: 'registrar_configuracion_asistencia', methods: ['POST'])]
    public function registrarConfiguracionAsistencia(Request $request): JsonResponse
    {
        // Obtener los datos enviados en el request
        $datos = json_decode($request->getContent(), true);

        if (!$datos || !isset($datos['tiempo_falta_horas'], $datos['tolerancia_ingreso_minutos'], $datos['permitir_foto'])) {
            return $this->json(['error' => 'Datos inválidos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Verificar si existe el id en los datos
        if (isset($datos['id'])) {
            $id = $datos['id'];
        } else {
            // Buscar el ID del grupo predeterminado si no se proporcionó el id
            $grupoPredeterminado = $this->grupoRepository->findOneBy([
                'grp_nombre' => 'Predeterminado'
            ]);

            $id = $this->configuracionAsistenciaRepository->findOneBy([
                'cas_grupo' => $grupoPredeterminado 
            ])->getId();
        }

        // Incorporar el id en los datos para la función de registrar
        $datos['id'] = $id;

        // Llamar a la función para registrar o actualizar la configuración de asistencia
        $estado = $this->configuracionAsistenciaFunciones->registrarDatosAsistencia($datos);

        if ($estado !== 'Ok') {
            return $this->json(['error' => $estado], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['message' => 'Configuración registrada o actualizada correctamente'], JsonResponse::HTTP_OK);
    }

    /**
     * API para obtener las notificaciones activas de asistencia del grupo predeterminado.
     *
     * @return JsonResponse
     */
    #[Route('/api/obtener-notificaciones-activas', name: 'obtener_notificaciones_activas', methods: ['GET'])]
    public function obtenerNotificacionesActivas(): JsonResponse
    {
        $grupoPredeterminado = $this->grupoRepository->findOneBy([
            'grp_nombre' => 'Predeterminado'
        ]);

        $configuracion = $this->configuracionAsistenciaRepository->findOneBy([
            'cas_grupo' => $grupoPredeterminado
        ]);

        if (!$configuracion) {
            return $this->json(['error' => 'Configuración no encontrada'], JsonResponse::HTTP_NOT_FOUND);
        }

        $notificaciones = $this->configuracionAsistenciaFunciones->obtenerNotificacionesActivas($configuracion->getId());

        if (!$notificaciones) {
            return $this->json(['error' => 'No se pudieron obtener las notificaciones activas'], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json($notificaciones, JsonResponse::HTTP_OK);
    }

    /**
     * API para actualizar las notificaciones activas de asistencia del grupo predeterminado.
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/actualizar-notificaciones-activas', name: 'actualizar_notificaciones_activas', methods: ['POST'])]
    public function actualizarNotificacionesActivas(Request $request): JsonResponse
    {
        $datos = json_decode($request->getContent(), true);

        if (!$datos || !isset($datos['faltas_tardanzas'], $datos['permisos'], $datos['vacaciones'], $datos['marcacion'])) {
            return $this->json(['error' => 'Datos inválidos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $grupoPredeterminado = $this->grupoRepository->findOneBy([
            'grp_nombre' => 'Predeterminado'
        ]);

        $configuracion = $this->configuracionAsistenciaRepository->findOneBy([
            'cas_grupo' => $grupoPredeterminado
        ]);

        if (!$configuracion) {
            return $this->json(['error' => 'Configuración no encontrada'], JsonResponse::HTTP_NOT_FOUND);
        }

        $estado = $this->configuracionAsistenciaFunciones->actualizarNotificacionesActivas($configuracion->getId(), $datos);

        if ($estado !== 'Ok') {
            return $this->json(['error' => $estado], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['message' => 'Notificaciones activas actualizadas correctamente'], JsonResponse::HTTP_OK);
    }

    /**
     * API para obtener la configuración de trabajo (área, puesto, modalidad, horario predeterminado) del grupo predeterminado.
     *
     * @return JsonResponse
     */
    #[Route('/api/obtener-configuracion-trabajo', name: 'obtener_configuracion_trabajo', methods: ['GET'])]
    public function obtenerConfiguracionTrabajo(): JsonResponse
    {
        $grupoPredeterminado = $this->grupoRepository->findOneBy([
            'grp_nombre' => 'Predeterminado'
        ]);

        $configuracion = $this->configuracionAsistenciaRepository->findOneBy([
            'cas_grupo' => $grupoPredeterminado
        ]);

        if (!$configuracion) {
            return $this->json(['error' => 'Configuración no encontrada'], JsonResponse::HTTP_NOT_FOUND);
        }

        $datos = $this->configuracionAsistenciaFunciones->obtenerConfiguracionTrabajo($configuracion->getId());

        if (!$datos) {
            return $this->json(['error' => 'No se pudieron obtener los datos de configuración de trabajo'], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json($datos, JsonResponse::HTTP_OK);
    }

    /**
     * API para actualizar la configuración de trabajo (área, puesto, modalidad, horario predeterminado) del grupo predeterminado.
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/actualizar-configuracion-trabajo', name: 'actualizar_configuracion_trabajo', methods: ['POST'])]
    public function actualizarConfiguracionTrabajo(Request $request): JsonResponse
    {
        $datos = json_decode($request->getContent(), true);

        if (!$datos || !isset($datos['area'], $datos['puesto'], $datos['modalidadTrabajo'], $datos['predeterminarHorario'])) {
            return $this->json(['error' => 'Datos inválidos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $grupoPredeterminado = $this->grupoRepository->findOneBy([
            'grp_nombre' => 'Predeterminado'
        ]);

        $configuracion = $this->configuracionAsistenciaRepository->findOneBy([
            'cas_grupo' => $grupoPredeterminado
        ]);

        if (!$configuracion) {
            return $this->json(['error' => 'Configuración no encontrada'], JsonResponse::HTTP_NOT_FOUND);
        }

        $estado = $this->configuracionAsistenciaFunciones->actualizarConfiguracionTrabajo($configuracion->getId(), $datos);

        if ($estado !== 'Ok') {
            return $this->json(['error' => $estado], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['message' => 'Configuración de trabajo actualizada correctamente'], JsonResponse::HTTP_OK);
    }
}
