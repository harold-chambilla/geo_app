import { defineStore } from 'pinia';
import axios from 'axios';

export const useHorarioStore = defineStore('horario', {
  state: () => ({
    areas: [], // Almacena las áreas, puestos y colaboradores obtenidos
    errorAreas: null // Para manejar errores al obtener las áreas
  }),
  getters: {},
  actions: {
    // Función para obtener áreas, puestos y colaboradores
    async fetchAreasEmpleados() {
      try {
        // Llamada a la nueva API de áreas con empleados
        const response = await axios.get('/empresa/horario/api/areas-empleados');
        
        // Almacenar los datos de áreas, puestos y colaboradores
        this.areas = response.data;
        this.errorAreas = null; // Limpiar cualquier error previo
      } catch (error) {
        // Manejo de error y limpiar datos
        this.areas = [];
        this.errorAreas = error.response?.data?.message || 'Error al obtener las áreas y empleados';
      }
    }
  }
});

