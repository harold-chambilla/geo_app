<template>
    <div class="container">
        <!-- Título con icono -->
        <div class="d-flex align-items-center mt-4 mb-3">
            <i class="bi bi-briefcase-fill fs-3 text-primary"></i>
            <h5 class="ms-2 mb-0 fw-bold">Área y puesto</h5>
        </div>

        <!-- Formulario para Área -->
        <div class="row">
            <div class="col-md-6">
                <label for="area" class="form-label fw-semibold">Área</label>
                <input v-model="nuevaArea" type="text" class="form-control mb-3" id="area" placeholder="Escribe el área...">
                <button @click="agregarArea" class="btn btn-primary mb-3">+ Agregar</button>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Áreas</label>
                <ul class="list-group mb-3">
                    <li v-for="area in opcionesStore.areas" :key="area.ara_id" class="list-group-item d-flex justify-content-between align-items-center">
                        {{ area.ara_nombre }}
                        <button 
                            v-if="area.ara_nombre.toLowerCase() !== 'sistema'"
                            @click="eliminarArea(area.ara_id)" 
                            class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div> 

        <!-- Formulario para Puesto -->
        <div class="row">
            <div class="col-md-6">
                <label for="select-area" class="form-label fw-semibold">Seleccionar Área</label>
                <select v-model="areaSeleccionadaId" @change="cargarPuestos" id="select-area" class="form-select mb-3">
                    <option selected disabled>Selecciona un área</option>
                    <option v-for="area in opcionesStore.areas" :key="area.ara_id" :value="area.ara_id">
                        {{ area.ara_nombre }}
                    </option>
                </select>

                <label for="puesto" class="form-label fw-semibold">Puesto</label>
                <input v-model="nuevoPuesto" type="text" class="form-control mb-3" id="puesto" placeholder="Escribe el puesto...">
                <button @click="agregarPuesto" class="btn btn-primary mb-3">+ Agregar</button>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Puestos</label>
                <ul class="list-group mb-3">
                    <li v-if="!areaSeleccionadaId" class="list-group-item text-muted">
                        Selecciona un área para ver sus puestos
                    </li>
                    <li v-else v-for="puesto in puestos" :key="puesto.pst_id" class="list-group-item d-flex justify-content-between align-items-center">
                        {{ puesto.pst_nombre }}
                        <button 
                            v-if="puesto.pst_nombre.toLowerCase() !== 'sistema' && puesto.pst_nombre.toLowerCase() !== 'superadministrador'" 
                            @click="eliminarPuesto(puesto.pst_id)" 
                            class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useOpcionesStore } from '@/store/empresa/opciones';

const opcionesStore = useOpcionesStore();

const nuevaArea = ref('');
const nuevoPuesto = ref('');
const areaSeleccionadaId = ref(null);
const puestos = ref([]);

// Obtener todas las áreas de la empresa en la carga inicial
onMounted(() => {
    const empresaId = 1; // ID de la empresa
    opcionesStore.fetchAreas(empresaId);
});

// Función para agregar una nueva área
function agregarArea() {
    const empresaId = 1; // ID de la empresa
    if (nuevaArea.value.trim() === '') return;
    opcionesStore.crearArea(empresaId, { ara_nombre: nuevaArea.value }).then(() => {
        nuevaArea.value = '';
    });
}

// Función para eliminar un área
function eliminarArea(areaId) {
    opcionesStore.eliminarArea(areaId);
}

// Cargar puestos de un área específica
function cargarPuestos() {
    if (areaSeleccionadaId.value) {
        opcionesStore.fetchArea(areaSeleccionadaId.value).then((data) => {
            puestos.value = data.puestos; // Asignar los puestos específicos del área seleccionada
        });
    }
}

// Función para agregar un nuevo puesto al área seleccionada
function agregarPuesto() {
    if (!areaSeleccionadaId.value || nuevoPuesto.value.trim() === '') return;
    opcionesStore.crearPuesto(areaSeleccionadaId.value, { pst_nombre: nuevoPuesto.value }).then(() => {
        nuevoPuesto.value = '';
        cargarPuestos(); // Recargar la lista de puestos
    });
}

// Función para eliminar un puesto
function eliminarPuesto(puestoId) {
    opcionesStore.eliminarPuesto(puestoId).then(() => {
        cargarPuestos(); // Recargar la lista de puestos
    });
}
</script>

