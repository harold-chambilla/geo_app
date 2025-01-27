<template>
    <div class="btn btn-white border rounded-pill text-dark fw-bold px-4 py-2 my-2 shadow-sm" style="background-color: white;">
        {{ currentHour }}
    </div>
</template>
<script setup>
import { ref, onMounted } from "vue";
import { useMarcadoStore } from '@/store/colaborador/marcado';

const marcadoStore = useMarcadoStore();

const currentHour = ref("");
const updateHour = () => {
    const now = new Date();
    currentHour.value = now.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit", hour12: true });
};

onMounted(() => {
    updateHour();
    marcadoStore.setHoraActual(currentHour.value);
    setInterval(updateHour, 60000);
});
</script>