<template>
    <div class="container mt-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <input type="date" v-model="fechaSeleccionada" class="form-control w-100 w-md-25 rounded-pill shadow-sm border-0 mb-2 mb-md-0 mr-1">
            <input v-model="busqueda" type="text" class="form-control w-100 w-md-25 rounded-pill shadow-sm border-0 ml-1" placeholder="Buscar">
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3 justify-content-center justify-content-md-start">
            <button class="btn btn-outline-light border rounded-pill shadow-sm text-dark" @click="toggleFiltro('area')">Área ▼</button>
            <button class="btn btn-outline-light border rounded-pill shadow-sm text-dark" @click="toggleFiltro('puesto')">Puesto ▼</button>
            <button class="btn btn-warning border rounded-pill shadow-sm text-dark" @click="toggleFiltro('ingreso')">Estado Ingreso ▼</button>
            <button class="btn btn-warning border rounded-pill shadow-sm text-dark" @click="toggleFiltro('salida')">Estado Salida ▼</button>
            <button class="btn btn-outline-light border rounded-pill shadow-sm text-dark" @click="toggleFiltro('modalidad')">Modalidad ▼</button>
        </div>
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
                            <th>Nombre</th>
                            <th>Puesto</th>
                            <th>Área</th>
                            <th>Fecha</th>
                            <th>Ingreso</th>
                            <th>Salida</th>
                            <th>Modalidad</th>
                            <th>H.extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="empleado in empleadosFiltrados" :key="empleado.id">
                            <td>{{ empleado.nombre }}</td>
                            <td>{{ empleado.puesto }}</td>
                            <td>{{ empleado.area }}</td>
                            <td>{{ empleado.fecha }}</td>
                            <td :class="getEstadoClase(empleado.ingreso)"><strong>{{ empleado.ingreso }}</strong></td>
                            <td :class="getEstadoClase(empleado.salida)"><strong>{{ empleado.salida }}</strong></td>
                            <td>{{ empleado.modalidad }}</td>
                            <td>{{ empleado.horasExtra }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const fechaSeleccionada = ref('2025-02-06');
const busqueda = ref('');
const filtrosActivos = ref({});
const empleados = ref([
    { id: 1, nombre: 'Juan Marco Sanchez Rodriguez', puesto: 'Asist. Contabilidad', area: 'Contabilidad', fecha: '2025-02-06', ingreso: '08:30', salida: '17:00', modalidad: 'Presencial', horasExtra: '15 min' },
    { id: 2, nombre: 'Maria Alejandra Linares Ramirez', puesto: 'Asist. Sistemas', area: 'Sistemas', fecha: '2025-02-06', ingreso: 'Vacaciones', salida: 'Vacaciones', modalidad: 'Presencial', horasExtra: '15 min' },
    { id: 3, nombre: 'Alex Martinez Perez', puesto: 'Jefe de Sistemas', area: 'Sistemas', fecha: '2025-02-06', ingreso: 'Vacaciones', salida: 'Vacaciones', modalidad: 'Presencial', horasExtra: '00 min' },
    { id: 4, nombre: 'José Mario Ruiz Rojas', puesto: 'Asist. Contabilidad', area: 'Contabilidad', fecha: '2025-02-06', ingreso: 'Permiso', salida: 'Permiso', modalidad: 'Presencial', horasExtra: '00 min' }
]);

const obtenerOpcionesFiltro = (filtro) => {
    return [...new Set(empleados.value.map(emp => emp[filtro]).filter(val => val))];
};

const toggleFiltro = (filtro) => {
    if (filtrosActivos.value[filtro]) {
        delete filtrosActivos.value[filtro];
    } else {
        filtrosActivos.value[filtro] = [];
    }
};

const empleadosFiltrados = computed(() => {
    return empleados.value.filter(emp =>
        emp.fecha === fechaSeleccionada.value &&
        (busqueda.value === '' || emp.nombre.toLowerCase().includes(busqueda.value.toLowerCase()) || emp.puesto.toLowerCase().includes(busqueda.value.toLowerCase())) &&
        Object.entries(filtrosActivos.value).every(([filtro, valores]) => valores.length === 0 || valores.includes(emp[filtro]))
    );
});

const getEstadoClase = (estado) => {
    if (estado === 'Vacaciones') return 'bg-light text-secondary fw-bold';
    if (estado === 'Permiso') return 'bg-light text-info fw-bold';
    return 'fw-bold';
};

watch(fechaSeleccionada, (newFecha) => {
    console.log("Fecha seleccionada:", newFecha);
});
</script>

<style>
body {
    background-color: #f8f9fa;
}
.table-hover tbody tr:hover {
    background-color: #f9f9f9;
}
.table thead {
    background-color: #fff7d6;
}
.table tbody tr:nth-child(odd) {
    background-color: #fffdf5;
}
.table tbody tr:nth-child(even) {
    background-color: #fefaf0;
}
.bg-light.text-info {
    color: #5bc0de !important;
    font-weight: bold;
}
.bg-light.text-secondary {
    color: #6c757d !important;
    font-weight: bold;
}
@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
    }
}
</style>