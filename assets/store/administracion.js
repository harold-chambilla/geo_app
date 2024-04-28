import { defineStore } from "pinia"
import axios from "axios";


export const administracionStore = defineStore('administracion', {
    state: () => ({
        asistencias: [],
        loading: false,
        error: null,
        // fecha: '',
       }),
    getters: {
        ASISTENCIAS(state) { return state.asistencias },
        ASISTENCIAS_FECHA(state) { return state.asistencias },
    },
    actions: {
        // async GET_ASISTENCIAS() {
        //     try {
        //     //   this.loading = true;
        //       const response = await axios.get('/administrador/api/asistencia');
        //       this.asistencias = response.data;
        //     //   this.loading = false;
        //     //   console.log('asis: ', this.asistencias)
        //     } catch (error) {
        //       this.loading = false;
        //       this.error = error.message; // O puedes manejar el error de otra manera
        //     }
        //   },
          // async GET_ASISTENCIAS_FECHA(fecha) {
          //   try {
          //   //   this.loading = true;
          //     const response = await axios.get(`/administrador/api/asistencia/${fecha}`);
          //     this.asistencias = response.data;
          //     console.log('asis: ', this.asistencias)
          //   } catch (error) {
          //       console.log('error GET: ', error.response.data)
          //       this.loading = false;
          //     this.error = error.message; // O puedes manejar el error de otra manera
          //   } 
          // },
          // async GET_ASISTENCIAS_FECHA(params) {
          //   try {
          //   //   this.loading = true;
          //     const response = await axios.get('/administrador/api/asistencia/filtros', {params});
          //     this.asistencias = response.data;
          //     console.log('asis: ', this.asistencias)
          //   } catch (error) {
          //       console.log('error GET: ', error.response.data)
          //       this.loading = false;
          //     this.error = error.message; // O puedes manejar el error de otra manera
          //   } 
          // },
          async GET_ASISTENCIAS_FECHA(querys) {
            try {
            //   this.loading = true;
              console.log('params: ', querys)
              const params = new URLSearchParams();
              querys.query ? params.append('query', querys.query): null;
              querys.fecha ? params.append('fecha', querys.fecha) : null;     
              querys.estado_entrada ? params.append('estado_entrada', querys.estado_entrada): null;
              querys.estado_salida ? params.append('estado_salida', querys.estado_salida): null;
              querys.puesto ? params.append('puesto', querys.puesto): null;
              querys.area ? params.append('area', querys.area): null;
              querys.modalidad ? params.append('modalidad', querys.modalidad): null;
              // params.append('estado_salida', querys.estado_salida);
              const response = await axios.get('/administrador/api/asistencia/filtros', {params: params});
              this.asistencias = response.data;
              console.log('asis: ', this.asistencias)
            } catch (error) {
                console.log('error GET: ', error.response.data)
                this.loading = false;
              this.error = error.message; // O puedes manejar el error de otra manera
            } 
          },
    }
})