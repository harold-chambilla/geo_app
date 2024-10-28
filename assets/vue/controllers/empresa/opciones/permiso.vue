<template>
    <div class="container">
        <!-- Título con icono -->
        <div class="d-flex align-items-center mt-4 mb-3">
            <i class="bi bi-key-fill fs-3" style="color: #003366;"></i>
            <h5 class="ms-2 mb-0 fw-bold">Permisos</h5>
        </div>

        <div class="row">
            <!-- Listado de motivos a la izquierda -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Motivos</label>
                <ul class="list-group mb-3">
                    <li v-if="store.motivos.length === 0" class="list-group-item text-muted">
                        No hay motivos disponibles.
                    </li>
                    <li v-for="motivo in store.motivos" :key="motivo.mtv_id" class="list-group-item d-flex justify-content-between align-items-center">
                        {{ motivo.mtv_nombre }}
                        <button @click="eliminarMotivo(motivo.mtv_id)" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Formulario de nuevo motivo a la derecha -->
            <div class="col-md-6">
                <label for="motivo" class="form-label fw-semibold">Motivo</label>
                <input v-model="nuevoMotivo" type="text" class="form-control mb-3" id="motivo" placeholder="Escribe el motivo...">
                <button @click="agregarMotivo" class="btn btn-primary mb-3">+ Agregar</button>
            </div>
        </div>

        <!-- Mensaje de éxito -->
        <div v-if="mensajeExito" class="alert alert-success mt-3" role="alert">
            {{ mensajeExito }}
        </div>
    </div>
</template>

<script setup>
import { useOpcionesStore } from '@/store/empresa/opciones';
import { ref, onMounted, watch } from 'vue';

// Inicializamos el store
const store = useOpcionesStore();
const empresaId = 1; // Reemplazar con el ID de la empresa actual
const nuevoMotivo = ref('');
const mensajeExito = ref('');

// Función para cargar los motivos al montar el componente
onMounted(() => {
    store.fetchMotivos(empresaId);
});

// Función para agregar un nuevo motivo
const agregarMotivo = async () => {
    if (nuevoMotivo.value.trim() !== '') {
        await store.registrarMotivo(empresaId, { mtv_nombre: nuevoMotivo.value });
        mensajeExito.value = 'El motivo se ha agregado correctamente.';
        nuevoMotivo.value = '';
    }
};

// Función para eliminar un motivo
const eliminarMotivo = async (motivoId) => {
    await store.eliminarMotivo(motivoId);
    mensajeExito.value = 'El motivo se ha eliminado correctamente.';
};

// Limpiar mensaje de éxito después de 3 segundos
watch(mensajeExito, (newValue) => {
    if (newValue) {
        setTimeout(() => {
            mensajeExito.value = '';
        }, 3000);
    }
});
</script>
