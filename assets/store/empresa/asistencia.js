import { defineStore } from "pinia";
import axios from "axios";

export const useAsistenciaStore = defineStore("asistenciaStore", {
  state: () => ({
    colaboradores: [],
    asistencias: [],
    status: null,
    error: null,
  }),

  getters: {
    getColaboradores: (state) => state.colaboradores,
    getAsistencias: (state) => state.asistencias,
    getStatus: (state) => state.status,
    getError: (state) => state.error,
  },

  actions: {
    async fetchColaboradores(empresaId, colaboradorId = null) {
      try {
        this.status = "loading";
        const response = await axios.post("/empresa/empleados/api/colaboradores", {
          empresa_id: empresaId,
          colaborador_id: colaboradorId,
        });
        this.colaboradores = response.data.data;
        this.status = "success";
      } catch (error) {
        this.status = "error";
        this.error = error.response?.data?.message || "Error al obtener colaboradores";
      }
    },
    async fetchAsistencias(colaboradores) {
        try {
          this.status = "loading";
          const response = await axios.post("/empresa/asistencia/api/obtener", {
            colaboradores: colaboradores, // Lista de IDs de colaboradores
          });
          this.asistencias = response.data;
          this.status = "success";
        } catch (error) {
          this.status = "error";
          this.error = error.response?.data?.message || "Error al obtener asistencias";
        }
      },
  },
});

