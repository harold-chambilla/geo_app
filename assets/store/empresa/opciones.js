import { defineStore } from 'pinia';
import axios from 'axios';

export const opcionesStore = defineStore('opciones', {
  state: () => ({
    ruc: null,
    razonSocial: null,
    areas: [], // Almacena las áreas obtenidas
    motivos: [], // Almacena los motivos obtenidos
    registroExitosoAreas: null, // Para manejar el éxito del registro de áreas
    registroExitosoMotivos: null, // Para manejar el éxito del registro de motivos
    errorRuc: null,
    errorRazonSocial: null,
    errorAreas: null, // Para manejar errores al obtener las áreas
    errorMotivos: null, // Para manejar errores al obtener los motivos
    errorRegistroAreas: null, // Para manejar errores en el registro de áreas
    errorRegistroMotivos: null, // Para manejar errores en el registro de motivos
  }),
  actions: {
    // Acción para obtener el RUC
    async fetchRuc() {
      try {
        const response = await axios.get('/empresa/opciones/api/ruc');
        this.ruc = response.data;
        this.errorRuc = null;
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
        this.errorRazonSocial = null;
      } catch (error) {
        this.razonSocial = null;
        this.errorRazonSocial = error.response?.data?.message || 'Error al obtener la razón social';
      }
    },

    // Acción para obtener las áreas
    async fetchAreas() {
      try {
        const response = await axios.get('/empresa/opciones/api/areas');
        this.areas = response.data;
        this.errorAreas = null;
      } catch (error) {
        this.areas = [];
        this.errorAreas = error.response?.data?.message || 'Error al obtener las áreas';
      }
    },

    // Acción para obtener los motivos
    async fetchMotivos() {
      try {
        const response = await axios.get('/empresa/opciones/api/motivos');
        this.motivos = response.data;
        this.errorMotivos = null;
      } catch (error) {
        this.motivos = [];
        this.errorMotivos = error.response?.data?.message || 'Error al obtener los motivos';
      }
    },

    // Acción para registrar áreas y puestos
    async registrarAreas(areas) {
      try {
        const response = await axios.post('/empresa/opciones/api/registrar-areas', areas);
        this.registroExitosoAreas = response.data.message;
        this.errorRegistroAreas = null;
      } catch (error) {
        this.registroExitosoAreas = null;
        this.errorRegistroAreas = error.response?.data?.message || 'Error al registrar áreas y puestos';
      }
    },

    // Acción para registrar motivos
    async registrarMotivos(motivos) {
      try {
        const response = await axios.post('/empresa/opciones/api/insertar-motivos', { motivos });
        this.registroExitosoMotivos = response.data.message;
        this.errorRegistroMotivos = null;
      } catch (error) {
        this.registroExitosoMotivos = null;
        this.errorRegistroMotivos = error.response?.data?.message || 'Error al registrar motivos';
      }
    }
  }
});





