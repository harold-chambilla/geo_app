import { defineStore } from "pinia";

export const useTimeStore = defineStore("timeStore", {
  state: () => ({
    timezone: "America/Lima", // Zona horaria predeterminada
    locale: "ES-PE", // Idioma predeterminado
    date: "", // Fecha obtenida de la API
    time: "", // Hora obtenida de la API
  }),

  getters: {
    getHoraActual: (state) => state.time || "", // Retorna la hora obtenida de la API
    getFechaActual: (state) => state.date || "", // Retorna la fecha obtenida de la API
    getZonaHoraria: (state) => state.timezone,
    getIdioma: (state) => state.locale,
  },

  actions: {
    async actualizarHora() {
      try {
        const response = await fetch(`/tiempo?timezone=${this.timezone}&locale=${this.locale}`);
        const data = await response.json();
        if (data.date && data.time) {
          this.date = data.date;
          this.time = data.time;
          this.timezone = data.timezone;
          this.locale = data.locale;
        }
      } catch (error) {
        console.error("Error obteniendo la hora desde la API:", error);
      }
    },

    cambiarZonaHoraria(nuevaZonaHoraria) {
      this.timezone = nuevaZonaHoraria;
      this.actualizarHora();
    },

    cambiarIdioma(nuevoIdioma) {
      this.locale = nuevoIdioma;
      this.actualizarHora();
    },

    iniciarSincronizacion() {
      this.actualizarHora();
      setInterval(() => {
        this.actualizarHora();
      }, 1000);
    },
  },

  persist: {
    enabled: true,
    strategies: [
      {
        key: "timeStore",
        storage: localStorage,
      },
    ],
  },
});