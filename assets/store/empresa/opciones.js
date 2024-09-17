import { defineStore } from 'pinia';
import axios from 'axios';

export const opcionesStore = defineStore('opciones', {
  state: () => ({
    ruc: null,
    razonSocial: null,
    errorRuc: null,
    errorRazonSocial: null,
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
    }
  }
});

