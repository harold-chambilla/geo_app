<template>
    <!-- Botones -->
    <div class="d-flex flex-column gap-3 p-3 text-center">
        <button class="btn btn-success fw-bold py-2" @click="marcarEntrada">
            ENTRADA
        </button>
        <button class="btn btn-danger fw-bold py-2" @click="marcarSalida">
            SALIDA
        </button>
    </div>
</template>
<script setup>
// Funciones de marcar
const marcarEntrada = () => {
    console.log("Entrada marcada");
};

const marcarSalida = () => {
    console.log("Salida marcada");
};
</script>