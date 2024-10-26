<template>
    <!-- Icono de la Empresa -->
    <div class="d-flex align-items-center mt-4 mb-3">
        <!-- Importar la imagen con require -->
        <img :src="require('@/img/empresa/logo.png')" alt="Icono Empresa" style="width: 50px; height: 50px;">
        <h5 class="ms-2 mb-0 fw-bold">Información de la Empresa</h5>
    </div>

    <!-- Company Information -->
    <div class="p-3 mb-4">
        <div v-if="loading">Cargando datos de la empresa...</div>
        <div v-else-if="error">{{ error }}</div>
        <div v-else>
            <div class="mb-3">
                <label for="ruc" class="form-label fw-semibold">RUC</label>
                <input type="text" class="form-control" id="ruc" :value="empresaData?.emp_ruc" readonly>
            </div>
            <div class="mb-3">
                <label for="razonSocial" class="form-label fw-semibold">Razón social</label>
                <input type="text" class="form-control" id="razonSocial" :value="empresaData?.emp_nombre" readonly>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useOpcionesStore } from '@/store/empresa/opciones'; // Import the store
import { onMounted, watch, ref } from 'vue';

const opcionesStore = useOpcionesStore(); // Initialize store
const { empresa, loading, error, fetchEmpresa } = opcionesStore;

// Explicit ref for empresa to track changes
const empresaData = ref(null);

// Watch the empresa value and log it to the console for debugging
watch(() => opcionesStore.empresa, (newEmpresa) => {
  empresaData.value = newEmpresa; // Sync with local ref
  console.log("Datos de la empresa recibidos:", newEmpresa);
});

onMounted(async () => {
  const empresaId = 1; // Set the ID of the company you want to fetch
  console.log("Fetching empresa data for ID:", empresaId);

  // Try fetching and log success or failure
  try {
    await fetchEmpresa(empresaId); // Call the action to fetch the company data
    console.log("Empresa data fetched successfully.");
  } catch (error) {
    console.error("Error fetching empresa data:", error);
  }
});
</script>
