<template>
    <div class="container mt-4">
        <!-- Separación entre la fecha y el campo de búsqueda -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <input 
                type="date" 
                v-model="fechaSeleccionada" 
                class="form-control w-100 w-md-25 rounded-pill shadow-sm border-0"
            >
            <input 
                v-model="busqueda" 
                type="text" 
                class="form-control w-100 w-md-25 rounded-pill shadow-sm border-0"
                placeholder="Buscar"
            >
        </div>

        <!-- Filtros -->
        <div class="d-flex flex-wrap gap-2 mt-3 justify-content-center justify-content-md-start">
            <button class="btn btn-outline-light border rounded-pill shadow-sm text-dark" @click="toggleFiltro('area')">Área ▼</button>
            <button class="btn btn-outline-light border rounded-pill shadow-sm text-dark" @click="toggleFiltro('puesto')">Puesto ▼</button>
            <button class="btn btn-warning border rounded-pill shadow-sm text-dark" @click="toggleFiltro('estado_entrada')">Estado Ingreso ▼</button>
            <button class="btn btn-warning border rounded-pill shadow-sm text-dark" @click="toggleFiltro('estado_salida')">Estado Salida ▼</button>
            <button class="btn btn-outline-light border rounded-pill shadow-sm text-dark" @click="toggleFiltro('modalidad')">Modalidad ▼</button>
        </div>

        <div class="d-flex flex-wrap gap-3 mt-3 justify-content-center">
            <div v-for="(valores, filtro) in filtrosActivos" :key="filtro" class="p-2 border rounded shadow-sm">
                <label class="form-label text-muted">{{ formatFiltroLabel(filtro) }}</label>
                <select v-model="filtrosActivos[filtro]" class="form-select w-100" multiple>
                    <option v-for="opcion in obtenerOpcionesFiltro(filtro)" :key="opcion" :value="opcion">{{ formatFiltroValor(filtro, opcion) }}</option>
                </select>
            </div>
        </div>

        <div class="card mt-3 p-3 shadow-sm border-0 rounded-4">
            <div v-if="!esPantallaPequena" class="table-responsive">
                <table class="table table-hover text-center">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Colaborador</th>
                            <th>Área</th>
                            <th>Puesto</th>
                            <th>Fecha</th>
                            <th>Ingreso</th>
                            <th>Salida</th>
                            <th>Modalidad</th>
                            <th>H.extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="asistencia in asistenciasFiltradas" :key="asistencia.id">
                            <td>{{ asistencia.id }}</td>
                            <td>{{ asistencia.colaborador }}</td>
                            <td>{{ capitalizeWords(asistencia.area) }}</td>
                            <td>{{ capitalizeWords(asistencia.puesto) }}</td>
                            <td>{{ formatearSoloFecha(asistencia.fecha_entrada) }}</td>
                            <td>
                                <span :class="getEstadoClase(asistencia.estado_entrada)">
                                    {{ asistencia.estado_entrada === 'pendiente' ? 'Pendiente' : asistencia.hora_entrada }}
                                </span>
                            </td>
                            <td>
                                <span :class="getEstadoClase(asistencia.estado_salida)">
                                    {{ asistencia.estado_salida === 'pendiente' ? 'Pendiente' : asistencia.hora_salida }}
                                </span>
                            </td>
                            <td>{{ formatModalidad(asistencia.modalidad) }}</td>
                            <td>
                                <span class="horas-extra-badge">
                                    {{ asistencia.horas_extra ? asistencia.horas_extra + ' min' : '00 min' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ✅ Versión de Tarjetas (Pantallas pequeñas) -->
            <div v-else class="tarjetas-responsive">
                <div v-for="asistencia in asistenciasFiltradas" :key="asistencia.id" class="card asistencia-card">
                    <div class="card-body">
                        <h5 class="card-title">{{ asistencia.colaborador }}</h5>
                        <p class="card-text"><strong>Fecha:</strong> {{ formatearSoloFecha(asistencia.fecha_entrada) }}</p>
                        <p class="card-text"><strong>Ingreso:</strong> 
                            <span :class="getEstadoClase(asistencia.estado_entrada)">
                                {{ asistencia.estado_entrada === 'pendiente' ? 'Pendiente' : asistencia.hora_entrada }}
                            </span>
                        </p>
                        <p class="card-text"><strong>Salida:</strong> 
                            <span :class="getEstadoClase(asistencia.estado_salida)">
                                {{ asistencia.estado_salida === 'pendiente' ? 'Pendiente' : asistencia.hora_salida }}
                            </span>
                        </p>
                        <p class="card-text"><strong>Modalidad:</strong> {{ formatModalidad(asistencia.modalidad) }}</p>
                        <p class="card-text"><strong>Horas Extra:</strong> 
                            <span class="horas-extra-badge">
                                {{ asistencia.horas_extra ? asistencia.horas_extra + ' min' : '00 min' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useTimeStore } from "@/store/tiempo.js";
import { useAsistenciaStore } from "@/store/empresa/asistencia.js";

const timeStore = useTimeStore();
const asistenciaStore = useAsistenciaStore();

const esPantallaPequena = ref(window.innerWidth < 940);

const actualizarAnchoPantalla = () => {
    esPantallaPequena.value = window.innerWidth < 940;
};

const busqueda = ref('');
const filtrosActivos = ref({});
const empresaId = 1; // ID de la empresa fija por el momento

// ✅ Computed para sincronizar la fecha con el store sin modificar la lógica de la fecha
const fechaSeleccionada = ref("");

// ✅ Función para convertir `DD-MM-YYYY` a `YYYY-MM-DD`
function convertirFechaAFormato(fecha) {
    if (!fecha) return "";
    if (/^\d{4}-\d{2}-\d{2}$/.test(fecha)) return fecha;
    if (/^\d{2}-\d{2}-\d{4}$/.test(fecha)) {
        const [dia, mes, año] = fecha.split("-");
        return `${año}-${mes}-${dia}`;
    }
    return fecha;
}

// ✅ Función para formatear solo la fecha `DD-MM-YYYY`
function formatearSoloFecha(fechaCompleta) {
    if (!fechaCompleta) return "";
    return convertirFechaAFormato(fechaCompleta.split(" ")[0]).split("-").reverse().join("-");
}

// ✅ Computed para filtrar asistencias según fecha y filtros
const asistenciasFiltradas = computed(() => {
    return asistenciaStore.getAsistencias.filter(asistencia => {
        const fechaAsistencia = convertirFechaAFormato(asistencia.fecha_entrada.split(" ")[0]);

        return fechaAsistencia === fechaSeleccionada.value &&
            (busqueda.value === '' || asistencia.colaborador.toLowerCase().includes(busqueda.value.toLowerCase())) &&
            Object.entries(filtrosActivos.value).every(([filtro, valores]) => valores.length === 0 || valores.includes(asistencia[filtro]));
    });
});

// ✅ Función para obtener opciones de filtros
const obtenerOpcionesFiltro = (filtro) => {
    return [...new Set(asistenciaStore.getAsistencias.map(asistencia => asistencia[filtro]).filter(val => val))];
};

// ✅ Función para activar o desactivar filtros
const toggleFiltro = (filtro) => {
    if (filtrosActivos.value[filtro]) {
        delete filtrosActivos.value[filtro];
    } else {
        filtrosActivos.value[filtro] = [];
    }
};

// ✅ Función para asignar clases de estado dinámicamente en `Ingreso` y `Salida`
const getEstadoClase = (estado) => {
    if (estado === 'pendiente') return 'badge bg-light text-muted p-2'; // Fondo gris claro
    if (estado === 'vacaciones') return 'badge bg-secondary text-white p-2'; // Fondo gris oscuro
    if (estado === 'permiso') return 'badge bg-info text-white p-2'; // Fondo celeste
    if (estado === 'tardanza') return 'badge bg-warning text-dark p-2'; // Fondo amarillo
    if (estado === 'puntual') return 'badge bg-success text-white p-2'; // Fondo verde
    return '';
};

const formatFiltroLabel = (filtro) => {
    const labels = {
        estado_entrada: "Estado Entrada",
        estado_salida: "Estado Salida",
        modalidad: "Modalidad",
        area: "Área",
        puesto: "Puesto"
    };
    return labels[filtro] || filtro;
};

const formatFiltroValor = (filtro, valor) => {
    if (filtro === "modalidad") return valor.includes("MOD_PRESENCIAL") ? "Presencial" : "Remoto";
    return capitalizeWords(valor);
};

const formatModalidad = (modalidad) => {
    return modalidad && modalidad.includes("MOD_PRESENCIAL") ? "Presencial" : "Remoto";
};

const capitalizeWords = (text) => {
    if (!text) return '';
    return text.toLowerCase().replace(/\b\w/g, char => char.toUpperCase());
};

// ✅ Obtener colaboradores y asistencias cuando el componente se monta
onMounted(async () => {
    console.log("🚀 Montando componente...");
    timeStore.iniciarSincronizacion();
    fechaSeleccionada.value = convertirFechaAFormato(timeStore.getFechaActual);

    await asistenciaStore.fetchColaboradores(empresaId);
    const idsColaboradores = asistenciaStore.getColaboradores.map(colaborador => colaborador.colaborador_id);

    if (idsColaboradores.length > 0) {
        await asistenciaStore.fetchAsistencias(idsColaboradores);
    }

    window.addEventListener("resize", actualizarAnchoPantalla);
});

onUnmounted(() => {
    window.removeEventListener("resize", actualizarAnchoPantalla);
});

// ✅ Observa cambios en la fecha seleccionada y recarga los datos
watch(fechaSeleccionada, async (newFecha) => {
    console.log("🕒 Fecha seleccionada ha cambiado:", newFecha);
    await asistenciaStore.fetchColaboradores(empresaId);
});
</script>

<style scoped>
    .horas-extra-badge {
        display: inline-block;
        padding: 5px 10px;
        border: 1px solid black;
        border-radius: 15px;
        font-weight: bold;
        font-size: 14px;
    }

    /* ✅ Estilos para tarjetas en versión móvil */
    .tarjetas-responsive {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
    }

    .asistencia-card {
        width: 100%;
        max-width: 400px;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        background-color: white;
    }

    .horas-extra-badge {
        display: inline-block;
        padding: 5px 10px;
        border: 1px solid black;
        border-radius: 15px;
        font-weight: bold;
        font-size: 14px;
    }
</style>
