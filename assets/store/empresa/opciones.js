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
    },
});
