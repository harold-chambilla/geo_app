import { defineStore } from 'pinia';
import axios from 'axios';

export const apiTesterStore = defineStore('apiTester', {
    state: () => ({
        response: null
    }),
    getters: {
        RESPONSE(state) {
            return state.response;
        },
    },
    actions: {
        async sendRequest(method, url, options) {
            try {
                // Ruta relativa ajustada para usar con proxy o bajo el mismo dominio
                let formdata = new FormData();
                formdata.append("url", url);
                formdata.append("method", method);
                formdata.append("options", options);

                const response = await axios.post('/empresa/api/tester/request', formdata);
                this.response = response.data;
            } catch (error) {
                // Manejo mejorado de errores
                if (error.response) {
                    // El servidor respondió con un código de estado fuera del rango de 2xx
                    this.response = {
                        message: `Error: ${error.message}`,
                        data: error.response.data,
                        status: error.response.status,
                    };
                } else if (error.request) {
                    // La solicitud fue hecha pero no se recibió ninguna respuesta
                    this.response = {
                        message: `No response received: ${error.message}`,
                        request: error.request,
                    };
                } else {
                    // Algo sucedió al configurar la solicitud que desencadenó un error
                    this.response = {
                        message: `Error setting up request: ${error.message}`,
                    };
                }
                console.error('Error:', error);
            }
        }
    }
});

