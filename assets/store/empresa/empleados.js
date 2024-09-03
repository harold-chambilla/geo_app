import { defineStore } from "pinia"
import axios from "axios";

export const empleadosStore = defineStore('empleados', {
    state: () => ({
        error: null,
    }),
    getters: {},
    actions: {
        async createUser(userData) {
              try {
                    let formData = new FormData();
                    formData.append('nombre', userData.nombre);
                    formData.append('apellidos', userData.apellido);
                    formData.append('dni', userData.dni);
                    formData.append('fecha_nacimiento', userData.fechaNacimiento);
                    formData.append('correo_electronico', userData.correoElectronico);
                    formData.append('nombre_usuario', userData.usuario);
                    formData.append('password', userData.contrasena);
                    formData.append('area', userData.area);
                    formData.append('puesto', userData.puesto);
                    formData.append('rol', JSON.stringify(userData.rol)); // Guardar el array de roles como una cadena JSON
                    formData.append('sede', userData.sede);

                    const response = await axios.post('/empresa/empleados/api/registro', formData);
                    this.user = response.data;
                    this.error = null;
              } catch (error) {
                    this.error = error.response?.data?.message || 'Error al crear el usuario';
                    this.user = null;
              }
        },
    }
})
