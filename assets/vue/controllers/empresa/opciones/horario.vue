<template>
    <div class="container">
        <!-- Título de la sección -->
        <div class="d-flex align-items-center mt-4 mb-3">
            <i class="bi bi-clock-fill fs-3" style="color: #003366;"></i>                  
            <h5 class="ms-2 mb-0 fw-bold">Horario</h5>
        </div>

        <div class="row g-3">
            <!-- Switch para Área -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label mb-0" for="areaSwitch">Área</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="areaSwitch" v-model="configuracion.cas_area">
                    </div>
                </div>
            </div>

            <!-- Switch para Puesto -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label mb-0" for="puestoSwitch">Puesto</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="puestoSwitch" v-model="configuracion.cas_puesto">
                    </div>
                </div>
            </div>

            <!-- Switch para Modalidad de trabajo -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label mb-0" for="modalidadSwitch">Modalidad de trabajo: 
                        <span class="fw-bold ms-1">{{ modalidadTexto }}</span>
                    </label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="modalidadSwitch" v-model="modalidadPresencial">
                    </div>
                </div>
            </div>

            <!-- Switch para Predeterminar horario -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label mb-0" for="horarioSwitch">Predeterminar horario</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="horarioSwitch" v-model="configuracion.cas_predhorario">
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón de guardar -->
        <div class="d-flex justify-content-center mt-4 mb-3">
            <button class="btn btn-primary" @click="guardarConfiguracion">Guardar</button>
        </div>

        <!-- Mensaje de guardado -->
        <div v-if="notification.message" :class="['alert', notification.type === 'success' ? 'alert-success' : 'alert-danger']" role="alert">
            {{ notification.message }}
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useOpcionesStore } from '@/store/empresa/opciones';

const opcionesStore = useOpcionesStore();

// Estado local para almacenar la configuración de horario
const configuracion = ref({
    cas_modalidad: false,
    cas_predhorario: false,
    cas_area: false,
    cas_puesto: false,
});

// Variable de control para modalidad
const modalidadPresencial = ref(true);

// Notificación para mostrar mensajes de éxito o error
const notification = ref({
    message: '',
    type: 'success'
});

// Texto de modalidad basado en el switch
const modalidadTexto = computed(() => (modalidadPresencial.value ? "Presencial" : "Remoto"));

// Función para mostrar el mensaje de guardado
function showNotification(message, type = 'success') {
    notification.value.message = message;
    notification.value.type = type;
    setTimeout(() => {
        notification.value.message = '';
    }, 3000); // Desaparece en 3 segundos
}

// Cargar configuración inicial al montar el componente
onMounted(async () => {
    const empresaId = 1; // Cambiar al ID real de la empresa
    await opcionesStore.fetchConfiguracionSistema(empresaId);

    configuracion.value = { ...opcionesStore.configuracionAsistencia };

    // Establecer el valor inicial de modalidadPresencial basado en cas_modalidad
    modalidadPresencial.value = configuracion.value.cas_modalidad.includes("MOD_PRESENCIAL");
});

// Guardar la configuración modificada
async function guardarConfiguracion() {
    configuracion.value.cas_modalidad = modalidadPresencial.value ? "['MOD_PRESENCIAL']" : "['MOD_REMOTO']";

    try {
        await opcionesStore.editarConfiguracionSistema({
            ...configuracion.value,
            empresa_id: 1, // Cambiar al ID real de la empresa
        });

        showNotification('Configuración de horario guardada exitosamente', 'success');
    } catch (error) {
        showNotification('Error al guardar la configuración de horario', 'error');
    }
}
</script>
