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
    sedes: []
  }),
  getters: {
    SEDES(state) { return state.sedes }
  },
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
    },

    async saveCoordinates(sede) {
      try {
        let formData = new FormData();
        // formData.append('sedeId', sede.id);
        formData.append('nombreEmpresa', sede.empresaNombre);
        formData.append('latitud', sede.latitude);
        formData.append('longitud', sede.longitude);
        formData.append('direccion', sede.direccion);
        formData.append('pais', sede.pais); 
        // formData.append('empresaId', sede.empresaId);
        formData.forEach((value, key) => {
          console.log(`${key}: ${value}`);
        });
        const response = await axios.post('/empresa/opciones/api/guardar/sedes', formData);
        console.log('Coordenadas guardadas exitosamente:', response.data);
        this.sedes.push(response.data);
      } catch (error) {
        console.error('Error al guardar las coordenadas:', error);
      }
    },

    async fetchSedes() {
      try {
        const response = await axios.get('/empresa/opciones/api/listar/sedes');
        console.log('apiconsumida')
        this.sedes = response.data;  // Guardar las sedes en el estado
        
      } catch (error) {
        console.error('Error al obtener las sedes:', error);
      }
    },
    async deleteSede(sedeId) {
      try {
        await axios.delete(`/empresa/opciones/api/borrar/sedes/${sedeId}`);
        this.sedes = this.sedes.filter(sede => sede.id !== sedeId);  // Remover la sede del estado
        console.log(`Sede con ID ${sedeId} eliminada.`);
      } catch (error) {
        console.error('Error al eliminar la sede:', error);
      }
    },

  }
});





