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
                this.asistencia = response.data.asistencia;
            } catch (error) {
                console.log(error.response.data);
            }
        },
        async PUT_ASISTENCIA(id, data) {
            try {
                const response = await axios.put(`/api/asistencia/${id}`, data);
                this.asistencia = response.data.asistencia;
            } catch (error) {
                console.log(error.response.data);
            }
        }
    }
})