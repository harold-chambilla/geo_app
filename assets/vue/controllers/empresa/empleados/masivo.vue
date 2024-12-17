<template>
  <div class="container mt-4">
    <div class="row mb-3">
      <!-- Descargar plantilla -->
      <div class="col-md-6">
        <button
          class="btn btn-success w-100 mb-3"
          @click="descargarPlantilla"
          :disabled="status === 'loading'"
        >
          <i class="bi bi-download me-2"></i> Descargar Plantilla Excel
        </button>
        <small class="text-muted">Descargue la plantilla para registrar colaboradores masivamente.</small>
      </div>

      <!-- Subir archivo -->
      <div class="col-md-6">
        <label for="fileUpload" class="form-label fw-semibold">Seleccione un archivo Excel</label>
        <input
          type="file"
          id="fileUpload"
          class="form-control"
          accept=".xlsx, .xls"
          @change="handleFileUpload"
        />
        <small class="text-muted mt-2 d-block">Suba el archivo con los datos de colaboradores.</small>
      </div>
    </div>

    <!-- Procesar archivo -->
    <div class="row">
      <div class="col-md-12 d-flex justify-content-center">
        <button
          class="btn btn-primary"
          @click="procesarArchivo"
          :disabled="!archivo || status === 'loading'"
        >
          <i class="bi bi-upload me-2"></i> Procesar Archivo
        </button>
      </div>
    </div>

    <!-- Mensajes de estado -->
    <div v-if="status === 'loading'" class="alert alert-info mt-4 text-center">
      <i class="bi bi-hourglass-split me-2"></i> Procesando archivo...
    </div>

    <!-- Colaboradores registrados exitosamente -->
    <div v-if="colaboradoresExitosos.length > 0" class="mt-4">
      <h6>Colaboradores Registrados Exitosamente:</h6>
      <div
        v-for="(colaborador, index) in colaboradoresExitosos"
        :key="index"
        class="alert alert-success"
        role="alert"
      >
        <ul class="mb-0">
          <li><strong>ID:</strong> {{ colaborador.colaborador_id }}</li>
          <li><strong>Nombre de Usuario:</strong> {{ colaborador.nombre_usuario }}</li>
          <li><strong>Nombres:</strong> {{ colaborador.nombres }}</li>
          <li><strong>Apellidos:</strong> {{ colaborador.apellidos }}</li>
          <li><strong>Grupo:</strong> {{ colaborador.grupo }}</li>
        </ul>
      </div>
    </div>

    <!-- Errores -->
    <div v-if="errores.length > 0" class="mt-4">
      <h6>Errores:</h6>
      <div
        v-for="(error, index) in errores"
        :key="index"
        class="alert alert-warning"
        role="alert"
      >
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Error:</strong> {{ error.error }}
        <ul>
          <li><strong>Sede:</strong> {{ error.colaborador.sede_id }}</li>
          <li><strong>Puesto:</strong> {{ error.colaborador.puesto_id }}</li>
          <li><strong>Nombre de Usuario:</strong> {{ error.colaborador.col_nombreusuario }}</li>
        </ul>
      </div>
    </div>

    <div v-if="status === 'error'" class="alert alert-danger mt-4 text-center">
      <i class="bi bi-x-circle me-2"></i> {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useEmpleadosStore } from "@/store/empresa/empleados";

// Store
const empleadosStore = useEmpleadosStore();
const archivo = ref(null);
const status = ref(null);
const error = ref(null);
const errores = ref([]);
const colaboradoresExitosos = ref([]);

// Descargar plantilla Excel
const descargarPlantilla = async () => {
  try {
    status.value = "loading";
    const plantillaURL = await empleadosStore.descargarPlantillaColaboradores(1); // Cambiar según el ID de la empresa
    const link = document.createElement("a");
    link.href = plantillaURL;
    link.download = "Plantilla_Colaboradores.xlsx";
    link.click();
    status.value = "success";
  } catch (err) {
    status.value = "error";
    error.value = "Error al descargar la plantilla. Intente nuevamente.";
  }
};

// Manejar archivo seleccionado
const handleFileUpload = (event) => {
  archivo.value = event.target.files[0];
};

// Procesar archivo subido
const procesarArchivo = async () => {
  if (!archivo.value) {
    error.value = "Por favor, seleccione un archivo.";
    status.value = "error";
    return;
  }

  try {
    const formData = new FormData();
    formData.append("empresa_id", 1); // Cambiar según el ID de la empresa
    formData.append("file", archivo.value);

    status.value = "loading";
    const response = await empleadosStore.registrarColaboradores(formData);

    // Separar resultados exitosos y errores
    colaboradoresExitosos.value = response.data.filter((item) => !item.error);
    errores.value = response.data.filter((item) => item.error);

    status.value = colaboradoresExitosos.value.length > 0 ? "partial-success" : "error";

    archivo.value = null; // Limpiar el archivo seleccionado
  } catch (err) {
    status.value = "error";
    error.value = err.response?.data?.message || "Error al procesar el archivo.";
  }
};
</script>

