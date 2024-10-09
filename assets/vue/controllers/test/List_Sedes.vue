<template>
    <table id="tablaSedes">
      <thead>
        <tr>
          <th class="Azul-prin">Sede</th>
          <th class="plomo">Dirección</th>
          <th>Opciones</th>
        </tr>
      </thead>
      <tbody id="tbodySedes">
        <tr v-for="sede in sedes" :key="sede.id">
          <td>{{ sede.nombre }}</td>
          <td>{{ sede.direccion }}</td>
          <td>
            <button @click="confirmDelete(sede.id)" class="btn-delete">
            <i class="fas fa-trash"></i> <!-- Icono de bote de basura -->
          </button>
          </td>
        </tr>
      </tbody>
    </table>
  </template>
  
  <script setup>
  import { computed, onMounted } from 'vue';
 import { opcionesStore } from '../../../store/empresa/opciones';
 import Swal from 'sweetalert2'
  
  // Instancia del store
  const opcionesStorage = opcionesStore();
  
  // Computed para acceder a las sedes almacenadas en el estado del store
  const sedes = computed(() => opcionesStorage.SEDES);

  onMounted(() =>{
    opcionesStorage.fetchSedes();
  })
  
//   // Cargar las sedes al montar el componente
//   onMounted(() => {
//     opcionesStorage.fetchSedes();  // Llamar a la API para obtener las sedes
//   });
  
//   // Función para eliminar una sede
//   const deleteSede = async (sedeId) => {
//     try {
//       await opcionesStorage.deleteSede(sedeId);  // Llamada a la acción del store para eliminar la sede
//       // Después de eliminar, vuelve a cargar las sedes para actualizar la tabla
//       opcionesStorage.fetchSedes();
//     } catch (error) {
//       console.error('Error al eliminar la sede:', error);
//     }
//   };

const confirmDelete = (sedeId) => {
  Swal.fire({
    title: '¿Está seguro?',
    text: "Esta acción no se puede deshacer.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sí, borrar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      opcionesStorage.deleteSede(sedeId);  // Llamar a la acción de borrar sede
      Swal.fire(
        'Eliminado',
        'La sede ha sido eliminada.',
        'success'
      );
    }
  });
};
  </script>
  
  <style scoped>

  </style>
  