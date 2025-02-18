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

        <!-- Sección de opciones de filtro -->
        <div class="d-flex flex-wrap gap-3 mt-3 justify-content-center">
            <div v-for="(valores, filtro) in filtrosActivos" :key="filtro" class="p-2 border rounded shadow-sm">
                <label class="form-label text-muted">{{ filtro }}</label>
                <select v-model="filtrosActivos[filtro]" class="form-select w-100" multiple>
                    <option v-for="opcion in obtenerOpcionesFiltro(filtro)" :key="opcion" :value="opcion">{{ opcion }}</option>
                </select>
            </div>
        </div>

        <div class="card mt-3 p-3 shadow-sm border-0 rounded-4">
            <div class="table-responsive">
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
                            <td>{{ asistencia.area }}</td>
                            <td>{{ asistencia.puesto }}</td>
                            <!-- ✅ Fecha sin hora -->
                            <td>{{ formatearSoloFecha(asistencia.fecha_entrada) }}</td>
                            <!-- Celda dinámica para Ingreso con colores -->
                            <td :class="getEstadoClase(asistencia.estado_entrada)">
                                <span v-if="asistencia.estado_entrada !== 'pendiente'">
                                    {{ asistencia.hora_entrada }}
                                </span>
                            </td>
                            <!-- Celda dinámica para Salida con colores -->
                            <td :class="getEstadoClase(asistencia.estado_salida)">
                                <span v-if="asistencia.estado_salida !== 'pendiente'">
                                    {{ asistencia.hora_salida }}
                                </span>
                            </td>
                            <td>{{ asistencia.modalidad }}</td>
                            <td>{{ asistencia.horas_extra }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useTimeStore } from "@/store/tiempo.js";
import { useAsistenciaStore } from "@/store/empresa/asistencia.js";

const timeStore = useTimeStore();
const asistenciaStore = useAsistenciaStore();

const busqueda = ref('');
const filtrosActivos = ref({});
const empresaId = 1; // ID de la empresa fija por el momento

// ✅ Computed para sincronizar la fecha con el store sin modificar la lógica de la fecha
const fechaSeleccionada = computed({
    get: () => {
        console.log("📅 Fecha en timeStore:", timeStore.getFechaActual);
        return convertirFechaAFormato(timeStore.getFechaActual);
    },
    set: (nuevaFecha) => {
        console.log("📅 Nueva fecha seleccionada:", nuevaFecha);
        timeStore.setFecha(nuevaFecha);
    }
});

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
    if (estado === 'Vacaciones') return 'badge bg-secondary text-white p-2'; // Fondo gris oscuro
    if (estado === 'Permiso') return 'badge bg-info text-white p-2'; // Fondo celeste
    if (estado === 'tardanza') return 'badge bg-warning text-dark p-2'; // Fondo amarillo
    if (estado === 'puntual') return 'badge bg-success text-white p-2'; // Fondo verde
    return '';
};

// ✅ Obtener colaboradores y asistencias cuando el componente se monta
onMounted(async () => {
    console.log("🚀 Montando componente...");
    timeStore.iniciarSincronizacion();

    await asistenciaStore.fetchColaboradores(empresaId);
    const idsColaboradores = asistenciaStore.getColaboradores.map(colaborador => colaborador.colaborador_id);

    if (idsColaboradores.length > 0) {
        await asistenciaStore.fetchAsistencias(idsColaboradores);
    }
});

// ✅ Observa cambios en la fecha seleccionada y recarga los datos
watch(fechaSeleccionada, async (newFecha) => {
    console.log("🕒 Fecha seleccionada ha cambiado:", newFecha);
    await asistenciaStore.fetchColaboradores(empresaId);
});
</script>
