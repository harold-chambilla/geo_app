import { defineStore } from "pinia";
import axios from "axios";

export const useEmpleadosStore = defineStore("empleadosStore", {
  state: () => ({
    colaboradores: [],
    sedes: [],
    areas: [],
    plantillaURL: null,
    status: null,
    error: null,
  }),

  getters: {
    getColaboradores: (state) => state.colaboradores,
    getSedes: (state) => state.sedes,
    getAreas: (state) => state.areas,
    getPlantillaURL: (state) => state.plantillaURL,
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

    async fetchSedes(empresaId) {
      try {
        this.status = "loading";
        const response = await axios.get(`/empresa/opciones/api/obtener-sedes/${empresaId}`);
        this.sedes = response.data.data;
        this.status = "success";
      } catch (error) {
        this.status = "error";
        this.error = error.response?.data?.message || "Error al obtener sedes";
      }
    },

    async fetchAreas(empresaId) {
      try {
        this.status = "loading";
        const response = await axios.get(`/empresa/opciones/api/obtener-areas/${empresaId}`);
        this.areas = response.data.data;
        this.status = "success";
      } catch (error) {
        this.status = "error";
        this.error = error.response?.data?.message || "Error al obtener áreas";
      }
    },

    async registrarColaboradores(payload) {
      try {
        this.status = "loading";
        const isFormData = payload instanceof FormData;

        const response = await axios.post(
          "/empresa/empleados/api/registrar-colaboradores",
          payload, // Puede ser FormData o JSON
          {
            headers: {
              "Content-Type": isFormData ? "multipart/form-data" : "application/json",
            },
          }
        );

        this.status = "success";
        return response.data;
      } catch (error) {
        this.status = "error";
        this.error = error.response?.data?.message || "Error al registrar colaboradores";
        throw error;
      }
    },

    async descargarPlantillaColaboradores(empresaId) {
      try {
        this.status = "loading";
        const response = await axios.get(`/empresa/empleados/api/descargar-plantilla-colaboradores/${empresaId}`, {
          responseType: "blob",
        });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        this.plantillaURL = url;
        this.status = "success";
        return url;
      } catch (error) {
        this.status = "error";
        this.error = error.response?.data?.message || "Error al descargar la plantilla";
        throw error;
      }
    },

    async modificarColaborador(payload) {
      try {
        this.status = "loading";
        const response = await axios.put("/empresa/empleados/api/colaboradores/editar", payload);
        this.status = "success";
        return response.data;
      } catch (error) {
        this.status = "error";
        this.error = error.response?.data?.message || "Error al modificar colaborador";
        throw error;
      }
    },

    async eliminarColaborador(colaboradorId) {
      try {
        this.status = "loading";
        const response = await axios.delete("/empresa/empleados/api/colaboradores/eliminar", {
          data: { colaborador_id: colaboradorId },
        });
        this.status = "success";
        return response.data;
      } catch (error) {
        this.status = "error";
        this.error = error.response?.data?.message || "Error al eliminar colaborador";
        throw error;
      }
    },
  },
});

