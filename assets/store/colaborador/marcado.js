import { defineStore } from "pinia";
import axios from "axios";

export const useMarcadoStore = defineStore("marcadoStore", {
  state: () => ({
    sede: null,
    horario: null,
    status: null,
    error: null,
  }),

  getters: {
    getSede: (state) => state.sede,
    getHorario: (state) => state.horario,
    getStatus: (state) => state.status,
    getError: (state) => state.error,
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
  },
});
