import { defineStore } from "pinia"
import axios from "axios";


export const asistenciaStore = defineStore('asistencia', {
    state: () => ({
        asistencia: '',
        entrada: null,
        salida: null,
        errorMessage: null,
       }),
    getters: {
        ENTRADA(state) { return state.entrada },
        SALIDA(state) { return state.salida },
        ASISTENCIA(state) { return state.asistencia }
    },
    actions: {
        async POST_ENTRADA(data) {
            try {
                console.log('daratatatata: ', data);
                let formData = new FormData();
                formData.append('asi_fechaentrada', data.fechaEntrada);
                formData.append('asi_horaentrada', data.horaEntrada);
                formData.append('asi_estadoentrada', data.estadoEntrada);
                formData.append('latitud', data.latitud);
                formData.append('longitud', data.longitud);
                formData.append('asi_fotoentrada', data.fotoEntrada);
                const response = await axios.post('/marcado/api/asistencia/entrada', formData); 
                // this.asistencia = response.data.asistencia;
                window.location.href = '/resultado';
            } catch (error) {
                this.errorMessage = error.response.data.message || 'Error desconocido';
                console.log(error.response.data);
                // alert(error);
            }
        },
        async POST_SALIDA(data) {
            try {
                let formData = new FormData();
                formData.append('asi_fechasalida', data.fechaSalida);
                formData.append('asi_horasalida', data.horaSalida);
                formData.append('asi_estadosalida', data.estadoSalida);
                formData.append('latitud', data.latitud);
                formData.append('longitud', data.longitud);
                formData.append('asi_fotosalida', data.fotoSalida);
                const response = await axios.post(`/marcado/api/asistencia/salida`, formData);
                // this.asistencia = response.data.asistencia;
                window.location.href = '/resultado';
            } catch (error) {
                this.errorMessage = error.response.data.message || 'Error desconocido';
                console.log(error.response.data);
            }
        },
        async GET_ASISTENCIA() {
            try {
              const response = await axios.get('/resultado/api/asistencia');
              this.entrada = response.data.entrada;
              this.salida = response.data.salida;
            } catch (error) {
              console.error('Error al obtener la asistencia:', error);
            }
          },
    }
})