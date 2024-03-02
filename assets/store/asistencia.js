import { defineStore } from "pinia"
import axios from "axios";


export const asistenciaStore = defineStore('asistencia', {
    state: () => ({
        asistencia: '',
       }),
    getters: {
    },
    actions: {
        async POST_ASISTENCIA(data) {
            try {
                const response = await axios.post('/api/asistencia', data);
                // this.asistencia = response.data.asistencia;
            } catch (error) {
                console.log(error.response.data);
            }
        },
        async PUT_ASISTENCIA(data) {
            try {
                const response = await axios.post(`/api/asistencia/salida`, data);
                this.asistencia = response.data.asistencia;
            } catch (error) {
                console.log(error.response.data);
            }
        }
    }
})