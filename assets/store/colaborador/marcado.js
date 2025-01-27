import { defineStore } from "pinia";
import axios from "axios";

export const useMarcadoStore = defineStore("marcadoStore", {
  state: () => ({
    sede: null,
    horario: null,
    status: null,
    error: null,
    ubicacion: {
      latitud: null,
      longitud: null,
      exactitud: null
    },
    distanciaSede: {
      distancia: null,
      dentroRadio: null
    },
    asistencias: [],
    horaActual: null,
  }),

  getters: {
    getSede: (state) => state.sede,
    getHorario: (state) => state.horario,
    getStatus: (state) => state.status,
    getError: (state) => state.error,
    getUbicacion: (state) => state.ubicacion,
    getDistanciaSede: (state) => state.distanciaSede,
    getAsistencias: (state) => state.asistencias,
    getHoraActual: (state) => state.horaActual,
  },

  actions: {
    /**
     * Obtiene la sede del colaborador basado en su ID
     * @param {number} colaboradorId
     */
    async fetchSede(colaboradorId) {
      try {
        this.status = "loading";
        const response = await axios.post("/inicio/api/sede", {
          colaborador_id: colaboradorId,
        });

        this.sede = response.data.data;
        this.status = "success";
      } catch (error) {
        this.status = "error";
        this.error = error.response?.data?.message || "Error al obtener sede";
      }
    },

    /**
     * Obtiene el horario de trabajo del colaborador para una fecha específica
     * @param {number} colaboradorId
     * @param {string} fecha (Opcional) Formato: "YYYY-MM-DD"
     */
    async fetchHorario(colaboradorId, fecha = null) {
      try {
        this.status = "loading";
        const payload = { colaborador_id: colaboradorId };
        if (fecha) {
          payload.fecha = fecha;
        }

        const response = await axios.post("/inicio/api/horario-colaborador", payload);

        this.horario = response.data.data;
        this.status = "success";
      } catch (error) {
        this.status = "error";
        this.error = error.response?.data?.message || "Error al obtener horario";
      }
    },
    /**
     * Crea una o varias asistencias.
     * @param {Object|Array} asistenciaData - Datos de la asistencia (objeto o array de asistencias).
     */
    async crearAsistencia(asistenciaData) {
      try {
        this.status = "loading";
        let payload = Array.isArray(asistenciaData) ? asistenciaData : [asistenciaData];
        const response = await axios.post("/asistencia/api/crear", payload);

        if (Array.isArray(response.data)) {
          this.asistencias = response.data;
        } else {
          this.asistencias = [response.data];
        }
        this.status = "success";
      } catch (error) {
        this.status = "error";
        this.error = error.response?.data?.error || "Error al crear asistencia";
      }
    },
    /**
     * Obtiene asistencia por ID, colaborador y/o fecha.
     * Puede devolver una única asistencia o un array de asistencias.
     * @param {Object} criteria - Criterios de búsqueda (id, colaborador_id, fecha).
     */
    async obtenerAsistencia(criteria) {
      try {
        this.status = "loading";
        const response = await axios.get("/asistencia/api/obtener", {
          params: criteria,
        });

        if (Array.isArray(response.data)) {
          this.asistencias = response.data;
        } else {
          this.asistencias = [response.data];
        }
        this.status = "success";
      } catch (error) {
        this.status = "error";
        this.error = error.response?.data?.error || "Error al obtener asistencia";
      }
    },
    /**
     * Actualiza una asistencia por ID.
     * @param {number} id - ID de la asistencia a actualizar.
     * @param {Object} asistenciaData - Datos a actualizar.
     */
    async actualizarAsistencia(id, asistenciaData) {
      try {
        this.status = "loading";
        const response = await axios.put(`/asistencia/api/actualizar/${id}`, asistenciaData);

        const index = this.asistencias.findIndex(asistencia => asistencia.id === id);
        if (index !== -1) {
          this.asistencias[index] = response.data;
        }

        this.status = "success";
      } catch (error) {
        this.status = "error";
        this.error = error.response?.data?.error || "Error al actualizar asistencia";
      }
    },
    /**
     * Elimina una asistencia de forma lógica.
     * @param {number|Array<number>} ids - ID o array de IDs de asistencias a eliminar.
     */
    async eliminarAsistencia(ids) {
      try {
        this.status = "loading";

        let payload = Array.isArray(ids) ? ids : [ids];

        const responses = await Promise.all(
          payload.map((id) => axios.delete(`/asistencia/api/eliminar/${id}`))
        );

        this.status = "success";
        return responses.map((response) => response.data);
      } catch (error) {
        this.status = "error";
        this.error = error.response?.data?.error || "Error al eliminar asistencia";
      }
    },
    setUbicacion(lat, lng, accuracy) {
      this.ubicacion = {
        latitud: lat,
        longitud: lng,
        exactitud: accuracy
      };
    },
    clearUbicacion() {
      this.ubicacion = {
        latitud: null,
        longitud: null,
        exactitud: null
      };
    },
    setDistanciaSede(distancia, dentroRadio) {
      this.distanciaSede = {
        distancia: distancia,
        dentroRadio: dentroRadio
      };
    },
    clearDistanciaSede() {
      this.distanciaSede = {
        distancia: null,
        dentroRadio: null
      };
    },
    setHoraActual(hora) {
      this.horaActual = hora;
    },
    clearHoraActual() {
      this.horaActual = null;
    },
  },
  persist: {
    enabled: true,
    strategies: [
      {
        key: "marcadoStore",
        storage: localStorage,
      },
    ],
  },
});