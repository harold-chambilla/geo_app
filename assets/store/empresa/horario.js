import { defineStore } from "pinia";
import axios from "axios";

export const useOpcionesStore = defineStore("opcionesStore", {
  state: () => ({
    empresa: null,
    areas: [],
    motivos: [],
    configuracionAsistencia: null,
    colaboradores: [],
    horarios: [],
    loading: false,
    error: null,
    sedes: [],
  }),
  getters: {
    GETSEDES(state) {
      return state.sedes;
    },
  },
  actions: {
    // Obtener áreas de una empresa
    async fetchAreas(empresaId) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.get(`/empresa/opciones/api/obtener-areas/${empresaId}`);
        if (response.data.status === "success") {
          this.areas = response.data.data;
        } else {
          throw new Error(response.data.message);
        }
      } catch (error) {
        this.error = error.message || "Error al obtener las áreas";
      } finally {
        this.loading = false;
      }
    },

    // Obtener colaboradores
    async fetchColaboradores(empresaId, colaboradorId = null) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post("/empresa/empleados/api/colaboradores", {
          empresa_id: empresaId,
          colaborador_id: colaboradorId,
        });
        if (response.data.status === "success") {
          this.colaboradores = response.data.data;
        } else {
          throw new Error(response.data.message);
        }
      } catch (error) {
        this.error = error.message || "Error al obtener colaboradores";
      } finally {
        this.loading = false;
      }
    },

    // Registrar horarios
    async registrarHorario(data) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post("/empresa/horario/api/registrar", data);
        if (response.data.status === "success") {
          this.horarios.push(...response.data.data);
        } else {
          throw new Error(response.data.message);
        }
      } catch (error) {
        this.error = error.message || "Error al registrar horario";
      } finally {
        this.loading = false;
      }
    },

    // Modificar un horario
    async modificarHorario(horarioId, data) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.put(`/empresa/horario/api/modificar/${horarioId}`, data);
        if (response.data.status === "success") {
          const index = this.horarios.findIndex((h) => h.id === horarioId);
          if (index !== -1) {
            this.horarios[index] = response.data.data;
          }
        } else {
          throw new Error(response.data.message);
        }
      } catch (error) {
        this.error = error.message || "Error al modificar horario";
      } finally {
        this.loading = false;
      }
    },

    // Eliminar un horario lógicamente
    async eliminarHorario(horarioId) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.delete(`/empresa/horario/api/eliminar/${horarioId}`);
        if (response.data.status === "success") {
          this.horarios = this.horarios.filter((h) => h.id !== horarioId);
        } else {
          throw new Error(response.data.message);
        }
      } catch (error) {
        this.error = error.message || "Error al eliminar horario";
      } finally {
        this.loading = false;
      }
    },

    // Obtener horarios de colaboradores
    async obtenerHorarios(data) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post("/empresa/horario/api/obtener", data);
        if (response.data.status === "success") {
          this.horarios = response.data.data;
        } else {
          throw new Error(response.data.message);
        }
      } catch (error) {
        this.error = error.message || "Error al obtener horarios";
      } finally {
        this.loading = false;
      }
    },
  },
});
