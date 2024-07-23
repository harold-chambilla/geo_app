<template>
    <div class="container mt-5">
      <h1>API Requester</h1>
      <form @submit.prevent="sendRequest" class="mb-3">
        <div class="mb-3">
          <label for="apiUrl" class="form-label">Target URL:</label>
          <input id="apiUrl" v-model="url" type="text" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="apiMethod" class="form-label">Method:</label>
          <select id="apiMethod" v-model="method" class="form-select" required>
            <option value="GET">GET</option>
            <option value="POST">POST</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="apiOptions" class="form-label">Options (JSON):</label>
          <textarea id="apiOptions" v-model="options" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Request</button>
      </form>
  
      <div v-if="response" class="response-area">
        <h2>Response</h2>
        <pre class="bg-light p-3 border">{{ response }}</pre>
      </div>
    </div>
</template>
  
<script setup>
    import { ref, computed } from 'vue';
    import { apiTesterStore } from '../../../store/empresa/api_tester';

    const url = ref('');
    const method = ref('GET');
    const options = ref([]); // Asegúrate de que este es un string JSON válido para parsear en el backend.
    const store = apiTesterStore();

    // Acceder al estado response del store de Pinia
    const response = computed(() => store.response);

    const sendRequest = async () => {
        await store.sendRequest(method.value, url.value, options.value);
    };
</script>
  
<style scoped>
  .response-area pre {
    white-space: pre-wrap; /* Respeta los espacios y saltos de línea */
    word-wrap: break-word; /* Asegura que el texto no desborde el contenedor */
  }
</style>