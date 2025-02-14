import { defineStore } from "pinia";

export const useTimeStore = defineStore("timeStore", {
  state: () => ({
    timezone: "America/Lima", // Zona horaria predeterminada
    locale: "es-PE", // Idioma predeterminado (puedes cambiarlo)
    currentTime: new Date().toISOString(), // Mantiene la hora en formato ISO
  }),

  getters: {
    /**
     * Obtiene la hora actual en formato HH:mm:ss según la zona horaria e idioma configurados.
     */
    getHoraActual: (state) => {
      return new Intl.DateTimeFormat(state.locale, {
        timeZone: state.timezone,
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false, // Usa formato de 24 horas
      }).format(new Date(state.currentTime));
    },

    /**
     * Obtiene la fecha actual en formato YYYY-MM-DD según la zona horaria e idioma configurados.
     */
    getFechaActual: (state) => {
      const formattedDate = new Intl.DateTimeFormat(state.locale, {
        timeZone: state.timezone,
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
      }).format(new Date(state.currentTime));

      // Convertir a formato YYYY-MM-DD, considerando diferentes formatos de salida
      const dateParts = formattedDate.match(/\d+/g);
      return dateParts.length === 3 ? `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}` : formattedDate;
    },

    /**
     * Obtiene la zona horaria actual
     */
    getZonaHoraria: (state) => state.timezone,

    /**
     * Obtiene el idioma actual
     */
    getIdioma: (state) => state.locale,
  },

  actions: {
    /**
     * Actualiza la hora actual en la zona horaria configurada
     */
    actualizarHora() {
      this.currentTime = new Date().toISOString();
    },

    /**
     * Cambia la zona horaria y actualiza la hora
     * @param {string} nuevaZonaHoraria - Ejemplo: "America/Bogota", "Europe/Madrid"
     */
    cambiarZonaHoraria(nuevaZonaHoraria) {
      this.timezone = nuevaZonaHoraria;
      this.actualizarHora();
    },

    /**
     * Cambia el idioma del formato de fecha/hora
     * @param {string} nuevoIdioma - Ejemplo: "es-ES", "fr-FR", "de-DE"
     */
    cambiarIdioma(nuevoIdioma) {
      this.locale = nuevoIdioma;
      this.actualizarHora();
    },

    /**
     * Inicia la sincronización de la hora cada segundo
     */
    iniciarSincronizacion() {
      this.actualizarHora();
      setInterval(() => {
        this.actualizarHora();
      }, 1000); // Se actualiza cada segundo
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
