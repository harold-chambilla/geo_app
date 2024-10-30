<template>
    <div class="container">
        <!-- Título de la sección -->
        <div class="d-flex align-items-center mt-4 mb-3">
            <i class="bi bi-bell-fill fs-3" style="color: #003366;"></i>           
            <h5 class="ms-2 mb-0 fw-bold">Notificaciones</h5>
        </div>

        <div class="row">
            <!-- Columna izquierda de switches -->
            <div class="col-md-6">
                <!-- Switch para faltas y tardanzas -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="form-check-label" for="notificacion1">
                        Envío por correo de faltas y tardanzas diaria
                    </label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="notificacion1" v-model="configuracion.cas_faltas_tardanzas">
                    </div>
                </div>

                <!-- Switch para permisos -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="form-check-label" for="notificacion2">
                        Envío al colaborador por correo de permisos creados
                    </label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="notificacion2" v-model="configuracion.cas_permisos">
                    </div>
                </div>
            </div>

            <!-- Columna derecha de switches -->
            <div class="col-md-6">
                <!-- Switch para vacaciones -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="form-check-label" for="notificacion3">
                        Envío al colaborador por correo de vacaciones creadas
                    </label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="notificacion3" v-model="configuracion.cas_vacaciones">
                    </div>
                </div>

                <!-- Switch para marcación -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="form-check-label" for="notificacion4">
                        Envío al colaborador por correo el registro de su marcación de asistencia
                    </label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="notificacion4" v-model="configuracion.cas_marcacion">
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
import { ref, onMounted } from 'vue';
import { useOpcionesStore } from '@/store/empresa/opciones';

const opcionesStore = useOpcionesStore();

// Estado local para almacenar la configuración de notificaciones
const configuracion = ref({
    cas_faltas_tardanzas: false,
    cas_permisos: false,
    cas_vacaciones: false,
    cas_marcacion: false,
});

// Notificación para mostrar mensajes de éxito o error
const notification = ref({
    message: '',
    type: 'success'
});

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

    // Asignar los valores iniciales a partir de la configuración obtenida
    configuracion.value = { ...opcionesStore.configuracionAsistencia };
});

// Guardar la configuración modificada
async function guardarConfiguracion() {
    try {
        await opcionesStore.editarConfiguracionSistema({
            ...configuracion.value,
            empresa_id: 1, // Cambiar al ID real de la empresa
        });

        showNotification('Configuración de notificaciones guardada exitosamente', 'success');
    } catch (error) {
        showNotification('Error al guardar la configuración de notificaciones', 'error');
    }
}
</script>

