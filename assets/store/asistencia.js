import { defineStore } from "pinia"
import axios from "axios";


export const asistenciaStore = defineStore('asistencia', {
    state: () => ({
        asistencia: '',
        entrada: null,
        salida: null,
       }),
    getters: {
        ENTRADA(state) { return state.entrada },
        SALIDA(state) { return state.salida },
        ASISTENCIA(state) { return state.asistencia }
    },
    actions: {
        async POST_ASISTENCIA(data) {
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
            } catch (error) {
                console.log(error.response.data);
            }
        },
        async PUT_ASISTENCIA(data) {
            try {
                let formData = new FormData();
                formData.append('asi_fechasalida', data.fechaSalida);
                formData.append('asi_horasalida', data.horaSalida);
                formData.append('asi_estadosalida', data.estadoSalida);
                formData.append('latitud', data.latitud);
                formData.append('longitud', data.longitud);
                formData.append('asi_fotosalida', data.fotoSalida);
                const response = await axios.post(`/marcado/api/asistencia/salida`, formData);
                this.asistencia = response.data.asistencia;
            } catch (error) {
                console.log(error.response.data);
            }
        },
        async GET_ENTRADA() {
            try {
              const response = await axios.get('/resultado/api/asistencia/entrada'); // Ruta de la API de entrada
            //   return response.data;
              this.entrada = response.data;
              
            } catch (error) {
              console.error('Error al obtener los datos de entrada:', error);
            }
          },
        async GET_SALIDA() {
            try {
              console.log("2hla")
              const response = await axios.get('/resultado/api/asistencia/salida'); // Ruta de la API de salida
              this.salida = response.data;
              console.log("Salida")
            //   return response.data;
            } catch (error) {
              console.error('Error al obtener los datos de salida:', error);
            }
          },
        async GET_ASISTENCIA() {
            try {
              const response = await axios.get('/resultado/api/asistencia/salida/prueba');
              this.entrada = response.data.entrada;
              this.salida = response.data.salida;
            } catch (error) {
              console.error('Error al obtener la asistencia:', error);
            }
          },
    }
})