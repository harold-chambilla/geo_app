import { defineStore } from 'pinia';
import axios from 'axios';

export const opcionesStore = defineStore('opciones', {
  state: () => ({
    ruc: null,
    razonSocial: null,
    areas: [], // Almacena las áreas obtenidas
    errorRuc: null,
    errorRazonSocial: null,
    errorAreas: null, // Para manejar errores al obtener las áreas
    registroExitoso: null, // Para manejar el éxito del registro
    errorRegistro: null // Para manejar errores en el registro de áreas
  }),
  actions: {
    // Acción para obtener el RUC
    async fetchRuc() {
      try {
        const response = await axios.get('/empresa/opciones/api/ruc');
        this.ruc = response.data;
        this.errorRuc = null; // Reseteamos el error si la solicitud es exitosa
      } catch (error) {
        this.ruc = null;
        this.errorRuc = error.response?.data?.message || 'Error al obtener el RUC';
      }
    },

    // Acción para obtener la razón social
    async fetchRazonSocial() {
      try {
        const response = await axios.get('/empresa/opciones/api/razonsocial');
        this.razonSocial = response.data;
        this.errorRazonSocial = null; // Reseteamos el error si la solicitud es exitosa
      } catch (error) {
        this.razonSocial = null;
        this.errorRazonSocial = error.response?.data?.message || 'Error al obtener la razón social';
      }
    },

    // Acción para obtener las áreas
    async fetchAreas() {
      try {
        const response = await axios.get('/empresa/opciones/api/areas');
        this.areas = response.data; // Guardamos las áreas en el estado
        this.errorAreas = null; // Reseteamos el error si la solicitud es exitosa
      } catch (error) {
        this.areas = [];
        this.errorAreas = error.response?.data?.message || 'Error al obtener las áreas';
      }
    },

    // Acción para registrar áreas y puestos
    async registrarAreas(areas) {
      try {
        const response = await axios.post('/empresa/opciones/api/registrar-areas', areas);
        this.registroExitoso = response.data.message; // Mensaje de éxito si la solicitud es exitosa
        this.errorRegistro = null; // Limpiar errores anteriores
      } catch (error) {
        this.registroExitoso = null;
        this.errorRegistro = error.response?.data?.message || 'Error al registrar áreas y puestos';
      }
    }
  }
});



