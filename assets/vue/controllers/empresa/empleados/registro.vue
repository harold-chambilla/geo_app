<template>
  <div class="container">
    <form @submit.prevent="registrarColaborador" class="mt-3">
      <div class="row">
        <!-- Columna 1 -->
        <div class="col-md-6">
          <div class="mb-3">
            <label for="sede" class="form-label fw-semibold">Sede</label>
            <select id="sede" class="form-select" v-model="sedeSeleccionada" required>
              <option value="" disabled>Seleccione una sede</option>
              <option v-for="sede in empleadosStore.getSedes" :key="sede.sed_id" :value="sede.sed_id">
                {{ sede.sed_nombre }}
              </option>
            </select>
          </div>

          <div class="mb-3">
            <label for="area" class="form-label fw-semibold">Área</label>
            <select id="area" class="form-select" v-model="areaSeleccionada" required>
              <option value="" disabled>Seleccione un área</option>
              <option v-for="area in areasFiltradas" :key="area.ara_id" :value="area.ara_id">
                {{ area.ara_nombre }}
              </option>
            </select>
          </div>

          <div class="mb-3">
            <label for="puesto" class="form-label fw-semibold">Puesto</label>
            <select id="puesto" class="form-select" v-model="puestoSeleccionado" required>
              <option value="" disabled>Seleccione un puesto</option>
              <option v-for="puesto in puestosFiltrados" :key="puesto.pst_id" :value="puesto.pst_id">
                {{ puesto.pst_nombre }}
              </option>
            </select>
          </div>

          <div class="mb-3">
            <label for="rol" class="form-label fw-semibold">Rol</label>
            <select id="rol" class="form-select" v-model="rolSeleccionado" required>
              <option value="" disabled>Seleccione un rol</option>
              <option v-for="rol in rolesFormateados" :key="rol.value" :value="rol.value">
                {{ rol.label }}
              </option>
            </select>
          </div>
        </div>

        <!-- Columna 2 -->
        <div class="col-md-6">
          <div class="mb-3">
            <label for="nombreUsuario" class="form-label fw-semibold">Nombre de Usuario</label>
            <input
              id="nombreUsuario"
              type="text"
              class="form-control"
              v-model="colaborador.col_nombreusuario"
              placeholder="Ingrese el nombre de usuario"
              required
            />
          </div>

          <div class="mb-3">
            <label for="nombres" class="form-label fw-semibold">Nombres</label>
            <input
              id="nombres"
              type="text"
              class="form-control"
              v-model="colaborador.col_nombres"
              placeholder="Ingrese los nombres"
              required
            />
          </div>

          <div class="mb-3">
            <label for="apellidos" class="form-label fw-semibold">Apellidos</label>
            <input
              id="apellidos"
              type="text"
              class="form-control"
              v-model="colaborador.col_apellidos"
              placeholder="Ingrese los apellidos"
              required
            />
          </div>

          <div class="mb-3">
            <label for="dni" class="form-label fw-semibold">DNI/NIT</label>
            <input
              id="dni"
              type="text"
              class="form-control"
              v-model="colaborador.col_dninit"
              placeholder="Ingrese el DNI/NIT"
              required
            />
          </div>

          <div class="mb-3">
            <label for="fechaNacimiento" class="form-label fw-semibold">Fecha de Nacimiento</label>
            <input
              id="fechaNacimiento"
              type="date"
              class="form-control"
              v-model="colaborador.col_fechainacimiento"
              required
            />
          </div>

          <div class="mb-3">
            <label for="correo" class="form-label fw-semibold">Correo Electrónico</label>
            <input
              id="correo"
              type="email"
              class="form-control"
              v-model="colaborador.col_correoelecronico"
              placeholder="Ingrese el correo electrónico"
              required
            />
          </div>

          <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Contraseña</label>
            <input
              id="password"
              type="password"
              class="form-control"
              v-model="colaborador.password"
              placeholder="Ingrese la contraseña"
              required
            />
          </div>
        </div>
      </div>

      <!-- Botón centrado -->
      <div class="d-flex justify-content-center">
        <button type="submit" class="btn btn-primary mt-3">Registrar</button>
      </div>
    </form>

    <!-- Mensajes de estado -->
    <div v-if="status === 'loading'" class="alert alert-info mt-3 text-center">
      Cargando...
    </div>
    <div v-if="status === 'success'" class="alert alert-success mt-3 text-center">
      Colaborador registrado exitosamente.
    </div>
    <div v-if="status === 'partial-success'" class="mt-3">
      <div
        v-for="(error, index) in errores"
        :key="index"
        class="alert alert-warning"
        role="alert"
      >
        <strong>Error para el usuario: {{ error.colaborador.col_nombreusuario }}</strong><br />
        <small>{{ error.error }}</small>
      </div>
    </div>
    <div v-if="status === 'error'" class="alert alert-danger mt-3 text-center">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useEmpleadosStore } from "@/store/empresa/empleados";

// Store
const empleadosStore = useEmpleadosStore();

// Estado local
const sedeSeleccionada = ref(null);
const areaSeleccionada = ref(null);
const puestoSeleccionado = ref(null);
const rolSeleccionado = ref(null);
const status = ref(null); // Control de estado del formulario
const error = ref(null);
const errores = ref([]); // Lista de errores para registros fallidos

// Datos del colaborador
const colaborador = ref({
  col_nombreusuario: "",
  col_nombres: "",
  col_apellidos: "",
  col_dninit: "",
  col_fechainacimiento: "",
  col_correoelecronico: "",
  password: "",
});

// Roles disponibles y sus etiquetas
const rolesFormateados = [
  { value: "ROLE_SUPERADMIN", label: "Super Administrador" },
  { value: "ROLE_ADMIN", label: "Administrador" },
  { value: "ROLE_COLABORADOR", label: "Colaborador" },
];

// Filtrar áreas excluyendo "sistema"
const areasFiltradas = computed(() => {
  return empleadosStore.getAreas.filter((area) => area.ara_nombre.toLowerCase() !== "sistema");
});

// Filtrar puestos dinámicamente según el área seleccionada
const puestosFiltrados = computed(() => {
  if (!areaSeleccionada.value) return [];
  const area = empleadosStore.getAreas.find((a) => a.ara_id === areaSeleccionada.value);
  return area
    ? area.puestos.filter((puesto) => puesto.pst_nombre.toLowerCase() !== "sistema")
    : [];
});

// Método para registrar el colaborador
const registrarColaborador = async () => {
  if (!sedeSeleccionada.value || !areaSeleccionada.value || !puestoSeleccionado.value || !rolSeleccionado.value) {
    error.value = "Por favor complete todos los campos.";
    status.value = "error";
    return;
  }

  const payload = {
    empresa_id: 1, // Cambiar según el ID de la empresa actual
    colaboradores: [
      {
        sede_id: sedeSeleccionada.value,
        puesto_id: puestoSeleccionado.value,
        roles: [rolSeleccionado.value],
        ...colaborador.value,
      },
    ],
  };

  try {
    status.value = "loading";
    const response = await empleadosStore.registrarColaboradores(payload);

    if (response.data.some((item) => item.error)) {
      // Capturar errores parciales
      errores.value = response.data.filter((item) => item.error);
      status.value = "partial-success";
    } else {
      // Registro exitoso completo
      status.value = "success";
      errores.value = [];
    }

    // Resetear formulario
    sedeSeleccionada.value = null;
    areaSeleccionada.value = null;
    puestoSeleccionado.value = null;
    rolSeleccionado.value = null;
    Object.keys(colaborador.value).forEach((key) => (colaborador.value[key] = ""));
  } catch (err) {
    status.value = "error";
    error.value = err.response?.data?.message || "Error al registrar colaborador.";
  }
};

// Cargar áreas y sedes al montar el componente
onMounted(() => {
  empleadosStore.fetchAreas(1); // Cambiar según el ID de la empresa actual
  empleadosStore.fetchSedes(1);
});
</script>
