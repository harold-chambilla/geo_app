import { defineStore } from "pinia"

export const funcionesStore = defineStore('funciones', {
    state: () => ({
             count: 0, 
             name: 'Eduardo',
             currentTime: '' 
            }),
    getters: {
        getCurrentFecha() {
            const now = new Date();
            const day = now.getDate().toString().padStart(2, '0');
            const month = (now.getMonth() + 1).toString().padStart(2, '0'); // Sumamos 1 porque los meses empiezan en 0
            const year = now.getFullYear().toString();
            return `${day}/${month}/${year}`;
        },
        getCurrentTiempo(state) {
            return state.currentTime;
        }  
    },
    actions: {
        iniciarTiempo() {
            this.tiempoActualizar(); // Actualizamos la hora inmediatamente
            setInterval(() => {
              this.tiempoActualizar(); // Actualizamos la hora cada segundo
            }, 1000);
        },
        tiempoActualizar() {
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            // Actualizamos la hora en el estado
            this.currentTime = `${hours}:${minutes}:${seconds}`;
        },
      increment() {
        this.count++
      },
    },
  })