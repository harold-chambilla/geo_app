<template>
    <div class="container"> 
        <!-- Título con icono -->
        <div class="d-flex align-items-center mt-4 mb-3">
            <i class="bi bi-briefcase-fill fs-3" style="color: #003366;"></i>
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
                    <li v-if="filtrarAreas(opcionesStore.areas).length === 0" class="list-group-item text-center text-muted">
                        No hay áreas disponibles
                    </li>
                    <li v-else v-for="area in filtrarAreas(opcionesStore.areas)" :key="area.ara_id" class="list-group-item d-flex justify-content-between align-items-center">
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
                    <option v-for="area in filtrarAreas(opcionesStore.areas)" :key="area.ara_id" :value="area.ara_id">
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
                    <li v-if="!areaSeleccionadaId" class="list-group-item text-center text-muted">
                        Selecciona un área para ver sus puestos
                    </li>
                    <li v-else-if="filtrarPuestos(puestos).length === 0" class="list-group-item text-center text-muted">
                        No hay puestos disponibles en esta área
                    </li>
                    <li v-else v-for="puesto in filtrarPuestos(puestos)" :key="puesto.pst_id" class="list-group-item d-flex justify-content-between align-items-center">
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

        <!-- Notificaciones -->
        <div v-if="notification.message" :class="['alert', notification.type === 'success' ? 'alert-success' : 'alert-danger']" role="alert">
            {{ notification.message }}
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

    // Notificación para mostrar mensajes de éxito o error
    const notification = ref({
        message: '',
        type: 'success'
    });

    // Función para mostrar notificación
    function showNotification(message, type = 'success') {
        notification.value.message = message;
        notification.value.type = type;
        setTimeout(() => {
            notification.value.message = '';
        }, 3000); // Desaparece en 3 segundos
    }

    // Obtener todas las áreas de la empresa en la carga inicial
    onMounted(() => {
        const empresaId = 1; // ID de la empresa
        opcionesStore.fetchAreas(empresaId);
    });

    // Función para agregar una nueva área
    async function agregarArea() {
        const empresaId = 1; // ID de la empresa
        if (nuevaArea.value.trim() === '') return;
        try {
            await opcionesStore.crearArea(empresaId, { ara_nombre: nuevaArea.value });
            showNotification("Área agregada correctamente", 'success');
            nuevaArea.value = '';
        } catch (error) {
            showNotification("Error al agregar el área", 'error');
        }
    }

    // Función para eliminar un área
    async function eliminarArea(areaId) {
        try {
            await opcionesStore.eliminarArea(areaId);
            showNotification("Área eliminada correctamente", 'success');
            
            // Resetear área seleccionada y lista de puestos
            areaSeleccionadaId.value = null;
            puestos.value = [];
        } catch (error) {
            showNotification("Error al eliminar el área", 'error');
        }
    }

    // Cargar puestos de un área específica
    async function cargarPuestos() {
        if (areaSeleccionadaId.value) {
            try {
                const data = await opcionesStore.fetchArea(areaSeleccionadaId.value);
                puestos.value = data.puestos; // Asignar los puestos específicos del área seleccionada
            } catch (error) {
                showNotification("Error al cargar los puestos", 'error');
            }
        }
    }

    // Función para agregar un nuevo puesto al área seleccionada
    async function agregarPuesto() {
        if (!areaSeleccionadaId.value || nuevoPuesto.value.trim() === '') return;
        try {
            await opcionesStore.crearPuesto(areaSeleccionadaId.value, { pst_nombre: nuevoPuesto.value });
            showNotification("Puesto agregado correctamente", 'success');
            nuevoPuesto.value = '';
            cargarPuestos(); // Recargar la lista de puestos
        } catch (error) {
            showNotification("Error al agregar el puesto", 'error');
        }
    }

    // Función para eliminar un puesto
    async function eliminarPuesto(puestoId) {
        try {
            await opcionesStore.eliminarPuesto(puestoId);
            showNotification("Puesto eliminado correctamente", 'success');
            cargarPuestos(); // Recargar la lista de puestos
        } catch (error) {
            showNotification("Error al eliminar el puesto", 'error');
        }
    }

    // Función para filtrar áreas que no sean 'sistema'
    function filtrarAreas(areas) {
        return areas.filter(area => area.ara_nombre.toLowerCase() !== 'sistema');
    }

    // Función para filtrar puestos que no sean 'sistema' ni 'superadministrador'
    function filtrarPuestos(puestos) {
        return puestos.filter(puesto => puesto.pst_nombre.toLowerCase() !== 'sistema' && puesto.pst_nombre.toLowerCase() !== 'superadministrador');
    }
</script>
