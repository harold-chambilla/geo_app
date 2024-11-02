<template>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="empresa-tab" data-bs-toggle="tab" data-bs-target="#empresa"
                type="button" role="tab" aria-controls="empresa" aria-selected="true">Empresa</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal"
                type="button" role="tab" aria-controls="personal" aria-selected="false">Personal</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="sistema-tab" data-bs-toggle="tab" data-bs-target="#sistema"
                type="button" role="tab" aria-controls="sistema" aria-selected="false">Sistema</button>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Empresa Tab -->
        <div class="tab-pane fade show active" id="empresa" role="tabpanel" aria-labelledby="empresa-tab">
            <eoempresa></eoempresa>
            <!-- Sucursales -->
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="bi bi-geo-alt-fill"
                        style="font-size: 2rem; color: #003366; text-shadow: 1px 1px 2px #001a33;"></i>
                    <h5 class="ms-2 mb-0 fw-bold">Sucursales</h5>
                </div>
                <sedeRegistro></sedeRegistro>
            </div>

            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th scope="col" class="fw-semibold">Sede</th>
                        <th scope="col" class="fw-semibold">Dirección</th>
                        <th scope="col" class="text-end fw-semibold">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="sede in sedes" :key="sede.id">
                        <td>{{ sede.sed_nombre }}</td>
                        <td>{{ sede.sed_direccion }}</td>
                        <td class="text-end">
                            <button class="btn btn-danger btn-sm" @click="eliminarSede(sede.id)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Personal Tab -->
        <div class="tab-pane fade" id="personal" role="tabpanel" aria-labelledby="personal-tab">
            <eoarea></eoarea>
            <eopermiso></eopermiso>
            <eoasistencia></eoasistencia>
        </div>

        <!-- Sistema Tab -->
        <div class="tab-pane fade" id="sistema" role="tabpanel" aria-labelledby="sistema-tab">
            <eonotificacion></eonotificacion>
            <eohorario></eohorario>
        </div>
    </div>
</template>

<script setup>
import eoempresa from './empresa.vue';
import eoarea from './area.vue';
import eopermiso from './permiso.vue';
import eoasistencia from './asistencia.vue';
import eonotificacion from './notificacion.vue';
import eohorario from './horario.vue';
import sedeRegistro from './sedeRegistro.vue';
import { useOpcionesStore } from '../../../../store/empresa/opciones';
import { computed, onMounted } from 'vue';

const opcionesStorage = useOpcionesStore();

const sedes = computed(() => opcionesStorage.GETSEDES);

// const sedesActivas = computed(() => sedeStore.sedes.filter(sede => !sede.sed_eliminado));

const eliminarSede = (id) => {
    opcionesStorage.listEliminado(id);
}

onMounted(() => {
    opcionesStorage.listSedes();
})
</script>
