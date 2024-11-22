<template>
  <div class="container mt-4">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th @click="ordenarPor('colaborador_id')" role="button" class="text-center">
              ID
              <span v-if="columnaOrdenada === 'colaborador_id'" class="ms-2">
                <i :class="ordenAscendente ? 'bi bi-arrow-up' : 'bi bi-arrow-down'"></i>
              </span>
            </th>
            <th @click="ordenarPor('nombre_usuario')" role="button">
              Nombre de Usuario
              <span v-if="columnaOrdenada === 'nombre_usuario'" class="ms-2">
                <i :class="ordenAscendente ? 'bi bi-arrow-up' : 'bi bi-arrow-down'"></i>
              </span>
            </th>
            <th @click="ordenarPor('nombres')" role="button">
              Nombres
              <span v-if="columnaOrdenada === 'nombres'" class="ms-2">
                <i :class="ordenAscendente ? 'bi bi-arrow-up' : 'bi bi-arrow-down'"></i>
              </span>
            </th>
            <th @click="ordenarPor('apellidos')" role="button">
              Apellidos
              <span v-if="columnaOrdenada === 'apellidos'" class="ms-2">
                <i :class="ordenAscendente ? 'bi bi-arrow-up' : 'bi bi-arrow-down'"></i>
              </span>
            </th>
            <th @click="ordenarPor('dni')" role="button">
              DNI
              <span v-if="columnaOrdenada === 'dni'" class="ms-2">
                <i :class="ordenAscendente ? 'bi bi-arrow-up' : 'bi bi-arrow-down'"></i>
              </span>
            </th>
            <th @click="ordenarPor('correo_electronico')" role="button">
              Correo Electrónico
              <span v-if="columnaOrdenada === 'correo_electronico'" class="ms-2">
                <i :class="ordenAscendente ? 'bi bi-arrow-up' : 'bi bi-arrow-down'"></i>
              </span>
            </th>
            <th @click="ordenarPor('puesto')" role="button">
              Puesto
              <span v-if="columnaOrdenada === 'puesto'" class="ms-2">
                <i :class="ordenAscendente ? 'bi bi-arrow-up' : 'bi bi-arrow-down'"></i>
              </span>
            </th>
            <th @click="ordenarPor('sede')" role="button">
              Sede
              <span v-if="columnaOrdenada === 'sede'" class="ms-2">
                <i :class="ordenAscendente ? 'bi bi-arrow-up' : 'bi bi-arrow-down'"></i>
              </span>
            </th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="colaborador in colaboradoresPaginados" :key="colaborador.colaborador_id">
            <td class="text-center">{{ colaborador.colaborador_id }}</td>
            <td>{{ colaborador.nombre_usuario }}</td>
            <td>{{ colaborador.nombres }}</td>
            <td>{{ colaborador.apellidos }}</td>
            <td>{{ colaborador.dni }}</td>
            <td>{{ colaborador.correo_electronico }}</td>
            <td>{{ colaborador.puesto }}</td>
            <td>{{ colaborador.sede }}</td>
            <td>
              <template v-if="colaborador.puesto.toLowerCase() !== 'superadministrador'">
                <i
                  class="bi bi-pencil-square text-warning me-2"
                  role="button"
                  data-bs-toggle="modal"
                  data-bs-target="#modalEditar"
                  @click="abrirModalEditar(colaborador)"
                ></i>
                <i
                  class="bi bi-trash text-danger"
                  role="button"
                  data-bs-toggle="modal"
                  data-bs-target="#modalEliminar"
                  @click="abrirModalEliminar(colaborador)"
                ></i>
              </template>
              <template v-else>
                <i class="bi bi-lock-fill text-secondary"></i>
              </template>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="colaboradoresOrdenados.length === 0" class="alert alert-warning text-center">
      No hay colaboradores registrados.
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
      <button class="btn btn-warning px-4" @click="recargarColaboradores">
        Recargar
      </button>
      <div>
        <button
          class="btn btn-outline-secondary px-4"
          :disabled="paginaActual === 1"
          @click="paginaActual--"
        >
          Anterior
        </button>
        <button
          class="btn btn-outline-secondary px-4 ms-2"
          :disabled="paginaActual === totalPaginas"
          @click="paginaActual++"
        >
          Siguiente
        </button>
      </div>
    </div>

    <!-- Modal Confirmar Eliminación -->
    <div
      class="modal fade"
      id="modalEliminar"
      tabindex="-1"
      aria-labelledby="modalEliminarLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalEliminarLabel">Confirmar Eliminación</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            ¿Está seguro de que desea eliminar al colaborador
            <strong>{{ colaboradorSeleccionado?.nombre_usuario }}</strong>?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              Cancelar
            </button>
            <button
              type="button"
              class="btn btn-danger"
              @click="confirmarEliminacion"
              data-bs-dismiss="modal"
            >
              Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Editar -->
    <div
      class="modal fade"
      id="modalEditar"
      tabindex="-1"
      aria-labelledby="modalEditarLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalEditarLabel">Editar Colaborador</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div v-if="alertaVisible" class="alert alert-success alert-dismissible fade show" role="alert">
              {{ mensajeAlerta }}
              <button type="button" class="btn-close" @click="cerrarAlerta" aria-label="Close"></button>
            </div>
            <form @submit.prevent="guardarEdicion">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Área</label>
                    <select class="form-select" v-model="areaSeleccionada" required>
                      <option value="" disabled>Seleccione un área</option>
                      <option
                        v-for="area in areasFiltradas"
                        :key="area.ara_id"
                        :value="area.ara_id"
                      >
                        {{ area.ara_nombre }}
                      </option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Puesto</label>
                    <select class="form-select" v-model="puestoSeleccionado" required>
                      <option value="" disabled>Seleccione un puesto</option>
                      <option
                        v-for="puesto in puestosFiltrados"
                        :key="puesto.pst_id"
                        :value="puesto.pst_id"
                      >
                        {{ puesto.pst_nombre }}
                      </option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Sede</label>
                    <select class="form-select" v-model="sedeSeleccionada" required>
                      <option value="" disabled>Seleccione una sede</option>
                      <option
                        v-for="sede in empleadosStore.getSedes"
                        :key="sede.sed_id"
                        :value="sede.sed_id"
                      >
                        {{ sede.sed_nombre }}
                      </option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Contraseña</label>
                    <input
                      type="password"
                      class="form-control"
                      v-model="colaboradorSeleccionado.password"
                      placeholder="Ingrese una nueva contraseña (opcional)"
                    />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre de Usuario</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="colaboradorSeleccionado.nombre_usuario"
                      required
                    />
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Nombres</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="colaboradorSeleccionado.nombres"
                      required
                    />
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Apellidos</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="colaboradorSeleccionado.apellidos"
                      required
                    />
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">DNI/NIT</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="colaboradorSeleccionado.dni"
                      required
                    />
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Correo Electrónico</label>
                    <input
                      type="email"
                      class="form-control"
                      v-model="colaboradorSeleccionado.correo_electronico"
                      required
                    />
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Fecha de Nacimiento</label>
                    <input
                      type="date"
                      class="form-control"
                      v-model="colaboradorSeleccionado.fecha_nacimiento"
                      required
                    />
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button
                  type="button"
                  class="btn btn-outline-secondary"
                  data-bs-dismiss="modal"
                >
                  Cancelar
                </button>
                <button type="submit" class="btn btn-primary">Guardar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useEmpleadosStore } from "@/store/empresa/empleados";

const empleadosStore = useEmpleadosStore();
const colaboradores = ref([]);
const paginaActual = ref(1);
const elementosPorPagina = 10;
const columnaOrdenada = ref('');
const ordenAscendente = ref(true);

const alertaVisible = ref(false);
const mensajeAlerta = ref('');

const cerrarAlerta = () => {
  alertaVisible.value = false;
};

const recargarColaboradores = async () => {
  try {
    await empleadosStore.fetchColaboradores(1);
    colaboradores.value = empleadosStore.getColaboradores || [];
  } catch (error) {
    console.error("Error al recargar colaboradores.");
  }
};

const colaboradoresOrdenados = computed(() => {
  if (!columnaOrdenada.value) return colaboradores.value;
  return [...colaboradores.value].sort((a, b) => {
    if (a[columnaOrdenada.value] < b[columnaOrdenada.value]) return ordenAscendente.value ? -1 : 1;
    if (a[columnaOrdenada.value] > b[columnaOrdenada.value]) return ordenAscendente.value ? 1 : -1;
    return 0;
  });
});

const colaboradoresPaginados = computed(() => {
  const inicio = (paginaActual.value - 1) * elementosPorPagina;
  const fin = inicio + elementosPorPagina;
  return colaboradoresOrdenados.value.slice(inicio, fin);
});

const totalPaginas = computed(() => Math.ceil(colaboradoresOrdenados.value.length / elementosPorPagina));

const ordenarPor = (columna) => {
  if (columnaOrdenada.value === columna) {
    ordenAscendente.value = !ordenAscendente.value;
  } else {
    columnaOrdenada.value = columna;
    ordenAscendente.value = true;
  }
};

const colaboradorSeleccionado = ref({});
const areaSeleccionada = ref(null);
const sedeSeleccionada = ref(null);
const puestoSeleccionado = ref(null);

const areasFiltradas = computed(() =>
  empleadosStore.getAreas.filter(
    (area) => area.ara_nombre.toLowerCase() !== "sistema"
  )
);

const puestosFiltrados = computed(() => {
  const area = empleadosStore.getAreas.find(
    (a) => a.ara_id === areaSeleccionada.value
  );
  return area
    ? area.puestos.filter(
        (puesto) => puesto.pst_nombre.toLowerCase() !== "sistema"
      )
    : [];
});

const abrirModalEditar = (colaborador) => {
  colaboradorSeleccionado.value = {
    ...colaborador,
    fecha_nacimiento: colaborador.fecha_nacimiento
      ? colaborador.fecha_nacimiento.split("T")[0]
      : "",
  };
  const area = empleadosStore.getAreas.find((a) =>
    a.puestos.some((p) => p.pst_nombre === colaborador.puesto)
  );
  areaSeleccionada.value = area?.ara_id || null;
  puestoSeleccionado.value =
    area?.puestos.find((p) => p.pst_nombre === colaborador.puesto)?.pst_id ||
    null;
  sedeSeleccionada.value = empleadosStore.getSedes.find(
    (sede) => sede.sed_nombre === colaborador.sede
  )?.sed_id;
};

const abrirModalEliminar = (colaborador) => {
  colaboradorSeleccionado.value = { ...colaborador };
};

const confirmarEliminacion = async () => {
  try {
    await empleadosStore.eliminarColaborador(colaboradorSeleccionado.value.colaborador_id);
    recargarColaboradores();
    console.log("Colaborador eliminado correctamente.");
  } catch (error) {
    console.error("Error al eliminar colaborador:", error.message);
  }
};

const guardarEdicion = async () => {
  try {
    const payload = {
      colaborador_id: colaboradorSeleccionado.value.colaborador_id,
      col_nombreusuario: colaboradorSeleccionado.value.nombre_usuario,
      col_nombres: colaboradorSeleccionado.value.nombres,
      col_apellidos: colaboradorSeleccionado.value.apellidos,
      col_dninit: colaboradorSeleccionado.value.dni,
      col_correoelecronico: colaboradorSeleccionado.value.correo_electronico,
      col_fechainacimiento: colaboradorSeleccionado.value.fecha_nacimiento,
      sede_id: sedeSeleccionada.value,
      puesto_id: puestoSeleccionado.value,
      password: colaboradorSeleccionado.value.password || null,
    };
    await empleadosStore.modificarColaborador(payload);
    recargarColaboradores();
    mensajeAlerta.value = "Colaborador modificado correctamente.";
    alertaVisible.value = true;
  } catch (error) {
    console.error("Error al modificar colaborador:", error.message);
  }
};

onMounted(() => {
  recargarColaboradores();
  empleadosStore.fetchSedes(1);
  empleadosStore.fetchAreas(1);
});
</script>
