import { defineStore } from 'pinia';
import axios from 'axios';

export const useOpcionesStore = defineStore('opcionesStore', {
    state: () => ({
        empresa: null,
        loading: false,
        error: null,
    }),
    actions: {
        // Acción para obtener la información de la empresa desde la API usando GET
        async fetchEmpresa(empresaId) {
            this.loading = true;
            this.error = null;

            try {
                // Usar axios para hacer una petición GET
                const response = await axios.get(`/empresa/opciones/api/obtener-empresa/${empresaId}`);

                if (response.data.status === 'success') {
                    this.empresa = response.data.data;
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                this.error = error.message || 'Ocurrió un error al obtener la información de la empresa';
            } finally {
                this.loading = false;
            }
        },
        async registrarSede(sede) {
            try {
                let formData = new FormData();
        // formData.append('sedeId', sede.id);
        formData.append('sed_nombre', sede.empresaNombre);
        formData.append('latitud', sede.latitude);
        formData.append('longitud', sede.longitude);
        formData.append('sed_direccion', sede.direccion);
        formData.append('sed_pais', sede.pais); 
              const response = await axios.post('/empresa/opciones/api/guardar/sede', formData);
              this.sedes.push(response.data); // Actualizar el estado con la nueva sede
              console.log('Sede registrada exitosamente:', response.data);
            } catch (error) {
              console.error('Error al registrar la sede:', error);
            }
          }
    },
});
