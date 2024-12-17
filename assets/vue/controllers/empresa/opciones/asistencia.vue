<template>
    <div class="container">
        <div class="d-flex align-items-center mt-4 mb-3">
            <i class="bi bi-person-check-fill fs-3" style="color: #003366;"></i>            
            <h5 class="ms-2 mb-0 fw-bold">Asistencia</h5>
        </div>  

        <div class="row g-4">
            <!-- Configuración general -->
            <div class="col-md-6">
                <div>
                    <h6 class="fw-bold mb-3">Configuración General</h6>
                    <div class="mb-3">
                        <label class="form-label">Tmp. de falta</label>
                        <div class="input-group">
                            <input type="number" class="form-control" v-model="configuracionGeneral.cas_tiempo_falta_horas" placeholder="2">
                            <span class="input-group-text">horas</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tolerancia de ingreso</label>
                        <div class="input-group">
                            <input type="number" class="form-control" v-model="configuracionGeneral.cas_tolerancia_ingreso_minutos" placeholder="10">
                            <span class="input-group-text">min.</span>
                        </div>
                    </div>

                    <div class="mb-3 d-flex align-items-center">
                        <label class="form-label me-2">Permitir foto</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" v-model="configuracionGeneral.cas_permitir_foto">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración de horas extra -->
            <div class="col-md-6">
                <div>
                    <h6 class="fw-bold mb-3">Horas Extra</h6>
                    <div class="mb-3">
                        <label class="form-label">Permitir horas extra</label>
                        <div class="input-group">
                            <select class="form-select" v-model="selectedArea">
                                <option selected disabled>Selecciona un área</option>
                                <option v-for="area in areasSinHorasExtras" :key="area.ara_id" :value="area.ara_id">
                                    {{ area.ara_nombre }}
                                </option>
                            </select>
                            <button class="btn btn-primary" @click="agregarAreaHorasExtra">Agregar</button>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Áreas seleccionadas</label>
                        <div class="p-3 border rounded">
                            <p class="text-center mb-0" v-if="!selectedAreas.length">No hay áreas seleccionadas</p>
                            <ul v-else class="list-group">
                                <li v-for="area in selectedAreas" :key="area.ara_id" class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ area.ara_nombre }}
                                    <button v-if="area.ara_nombre.toLowerCase() !== 'sistema'" class="btn btn-danger btn-sm" @click="eliminarAreaHorasExtra(area.ara_id)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4 mb-3">
            <div class="col-12 d-flex justify-content-center">
                <button class="btn btn-primary" @click="guardarConfiguracion">Guardar</button>
            </div>
        </div>

        <!-- Mensaje de guardado -->
        <div v-if="notification.message" :class="['alert', notification.type === 'success' ? 'alert-success' : 'alert-danger']" role="alert">
            {{ notification.message }}
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useOpcionesStore } from '@/store/empresa/opciones';

const opcionesStore = useOpcionesStore();
const selectedArea = ref(null);
const selectedAreas = ref([]);
const configuracionGeneral = ref({});

// Notificación para mostrar mensajes de éxito o error
const notification = ref({
    message: '',
    type: 'success'
});

// Temporal para almacenar áreas agregadas o eliminadas
const areasParaAgregar = ref([]);
const areasParaEliminar = ref([]);

// Función para mostrar el mensaje de guardado
function showNotification(message, type = 'success') {
    notification.value.message = message;
    notification.value.type = type;
    setTimeout(() => {
        notification.value.message = '';
    }, 3000); // Desaparece en 3 segundos
}

// Inicializar configuración y áreas al montar el componente
onMounted(async () => {
    const empresaId = 1; // Cambiar al ID real de la empresa
    await opcionesStore.fetchConfiguracionSistema(empresaId);
    await opcionesStore.fetchAreas(empresaId);

    configuracionGeneral.value = { ...opcionesStore.configuracionAsistencia };
    selectedAreas.value = opcionesStore.areas.filter(area => area.horas_extras && area.ara_nombre.toLowerCase() !== 'sistema');
});

// Computed para obtener las áreas sin horas extras activadas y sin el área "Sistema"
const areasSinHorasExtras = computed(() => {
    return opcionesStore.areas.filter(area => !area.horas_extras && area.ara_nombre.toLowerCase() !== 'sistema');
});

// Agregar un área para activar horas extras
function agregarAreaHorasExtra() {
    if (selectedArea.value && !selectedAreas.value.some(area => area.ara_id === selectedArea.value)) {
        const area = areasSinHorasExtras.value.find(area => area.ara_id === selectedArea.value);
        if (area) {
            selectedAreas.value.push(area);
            areasParaAgregar.value.push(area); // Agregar al temporal
            areasParaEliminar.value = areasParaEliminar.value.filter(a => a.ara_id !== area.ara_id); // Remover de la lista de eliminados si está
        }
    }
    selectedArea.value = null;
}

// Eliminar un área de la lista de horas extras y registrar el cambio
function eliminarAreaHorasExtra(areaId) {
    const area = selectedAreas.value.find(area => area.ara_id === areaId);
    if (area && area.ara_nombre.toLowerCase() !== 'sistema') { // Evita eliminar si es "Sistema"
        areasParaEliminar.value.push(area); // Agregar al temporal para eliminar
        selectedAreas.value = selectedAreas.value.filter(area => area.ara_id !== areaId);
        areasParaAgregar.value = areasParaAgregar.value.filter(a => a.ara_id !== areaId); // Remover de la lista de agregados si está
    }
}

// Guardar configuración de asistencia
async function guardarConfiguracion() {
    try {
        // Guardar configuración general
        await opcionesStore.editarConfiguracionSistema({ ...configuracionGeneral.value, empresa_id: 1 });

        // Aplicar cambios de horas extras solo para las áreas marcadas
        for (const area of areasParaAgregar.value) {
            await opcionesStore.editarConfiguracionSistema({ cas_horas_extras: true, area_id: area.ara_id });
        }
        for (const area of areasParaEliminar.value) {
            await opcionesStore.editarConfiguracionSistema({ cas_horas_extras: false, area_id: area.ara_id });
        }

        // Limpiar temporales y mostrar notificación
        areasParaAgregar.value = [];
        areasParaEliminar.value = [];
        showNotification('Configuración guardada exitosamente', 'success');
    } catch (error) {
        showNotification('Error al guardar la configuración', 'error');
    }
}
</script>
