import { defineStore } from 'pinia';
import axios from 'axios';

export const useOpcionesStore = defineStore('opcionesStore', {
    state: () => ({
        empresa: null,
        areas: [], // Asegúrate de que `areas` esté inicializado como un array vacío
        motivos: [], // Nueva propiedad para almacenar los motivos
        configuracionAsistencia: null, // Almacena la configuración de asistencia del sistema o de un área
        loading: false,
        error: null,
        sedes: []
    }),
    getters: {
        GETSEDES(state) { return state.sedes },
    },
    actions: {
        // Acción para obtener la información de la empresa desde la API usando GET
        async fetchEmpresa(empresaId) {
            this.loading = true;
            this.error = null;

            try {
                // Usar axios para hacer una petición GET
                const response = await axios.get(`/empresa/opciones/api/obtener-empresa/${empresaId}`);

                if (response.data.status === 'success') {
                    this.empresa = response.data.data;
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                this.error = error.message || 'Ocurrió un error al obtener la información de la empresa';
            } finally {
                this.loading = false;
            }
        },
        async registrarSede(sede) {
            try {
                let formData = new FormData();
                // formData.append('sedeId', sede.id);
                formData.append('sed_nombre', sede.empresaNombre);
                formData.append('latitud', sede.latitude);
                formData.append('longitud', sede.longitude);
                formData.append('sed_direccion', sede.direccion);
                formData.append('sed_pais', sede.pais);
                const response = await axios.post('/empresa/opciones/api/guardar/sede', formData);
                // this.sedes = response.data;
                // console.log('dataaaa: ', response.data)
                  this.sedes.push(response.data.sede); // Actualizar el estado con la nueva sede
            } catch (error) {
                console.error('Error al registrar la sede:', error);
            }
        },
        async listEliminado(id) {
            try {
                const response = await axios.patch(`/empresa/opciones/api/sedes/${id}/eliminar`);
                //   this.sedes = response.data;
                // this.sedes = this.sedes.filter(sede => !sede.sed_eliminado);
                const updatedSede = response.data.sede;

                // Filtrar la sede eliminada del array de sedes en el store
                this.sedes = this.sedes.filter(sede => sede.id !== updatedSede.id || !updatedSede.sed_eliminado);

                // Eliminar la sede de la lista si está eliminada
                // this.sedes = this.sedes.filter(s => s.id !== updatedSede.id || !updatedSede.sed_eliminado);
                //   this.fetchSedes();  // Recargar el listado después de cambiar el estado
            } catch (error) {
                console.error('Error al cambiar el estado de eliminación:', error);
            }
        },
        async listSedes() {
            try {
                const response = await axios.get('/empresa/opciones/api/listar/sedes');
                this.sedes = response.data.sede;
            } catch (error) {
                console.error('Error al obtener sedes:', error);
            }
        },


        // Acción para crear un área
        async crearArea(empresaId, areaData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post(`/empresa/opciones/api/crear-area/${empresaId}`, areaData);
                if (response.data.status === 'success') {
                    this.areas.push(response.data.data);
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                this.error = error.message || 'Error al crear el área';
            } finally {
                this.loading = false;
            }
        },

        // Acción para obtener todas las áreas de una empresa
        async fetchAreas(empresaId) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(`/empresa/opciones/api/obtener-areas/${empresaId}`);
                if (response.data.status === 'success') {
                    this.areas = response.data.data;
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                this.error = error.message || 'Error al obtener las áreas';
            } finally {
                this.loading = false;
            }
        },

        // Acción para obtener un área por su ID
        async fetchArea(areaId) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(`/empresa/opciones/api/obtener-area/${areaId}`);
                if (response.data.status === 'success') {
                    return response.data.data; // Retorna el área específica
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                this.error = error.message || 'Error al obtener el área';
            } finally {
                this.loading = false;
            }
        },

        // Acción para eliminar un área
        async eliminarArea(areaId) {
            try {
                const response = await axios.delete(`/empresa/opciones/api/eliminar-area/${areaId}`);
                if (response.data.status === 'success') {
                    this.areas = this.areas.filter(area => area.ara_id !== areaId);
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                this.error = error.message || 'Error al eliminar el área';
            }
        },

        // Acción para crear un puesto en un área específica
        async crearPuesto(areaId, puestoData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post(`/empresa/opciones/api/crear-puesto/${areaId}`, puestoData);
                if (response.data.status === 'success') {
                    this.puestos.push(response.data.data);
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                this.error = error.message || 'Error al crear el puesto';
            } finally {
                this.loading = false;
            }
        },

        // Acción para obtener un puesto por su ID
        async fetchPuesto(puestoId) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(`/empresa/opciones/api/obtener-puesto/${puestoId}`);
                if (response.data.status === 'success') {
                    return response.data.data; // Retorna el puesto específico
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                this.error = error.message || 'Error al obtener el puesto';
            } finally {
                this.loading = false;
            }
        },

        // Acción para eliminar un puesto
        async eliminarPuesto(puestoId) {
            try {
                const response = await axios.delete(`/empresa/opciones/api/eliminar-puesto/${puestoId}`);
                if (response.data.status === 'success') {
                    this.puestos = this.puestos.filter(puesto => puesto.pst_id !== puestoId);
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                this.error = error.message || 'Error al eliminar el puesto';
            }
        },

        // Acción para registrar un motivo
        async registrarMotivo(empresaId, motivoData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post(`/empresa/opciones/api/registrar-motivo/${empresaId}`, motivoData);
                if (response.data.status === 'success') {
                    this.motivos.push(response.data.data); // Añadir el nuevo motivo al estado
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                this.error = error.message || 'Error al registrar el motivo';
            } finally {
                this.loading = false;
            }
        },
        // Acción para obtener todos los motivos de una empresa
        async fetchMotivos(empresaId) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(`/empresa/opciones/api/obtener-motivos/${empresaId}`);
                if (response.data.status === 'success') {
                    this.motivos = response.data.data; // Asignar los motivos al estado
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                this.error = error.message || 'Error al obtener los motivos';
            } finally {
                this.loading = false;
            }
        },

        // Acción para eliminar un motivo
        async eliminarMotivo(motivoId) {
            try {
                const response = await axios.delete(`/empresa/opciones/api/eliminar-motivo/${motivoId}`);
                if (response.data.status === 'success') {
                    this.motivos = this.motivos.filter(motivo => motivo.mtv_id !== motivoId); // Remover el motivo eliminado
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                this.error = error.message || 'Error al eliminar el motivo';
            }
        },

        // Obtener configuración de asistencia "sistema" de una empresa
        async fetchConfiguracionSistema(empresaId) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(`/empresa/opciones/api/obtener-configuracion-sistema/${empresaId}`);
                if (response.data.status === 'success') {
                    this.configuracionAsistencia = response.data.data;
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                this.error = error.message || 'Error al obtener la configuración del sistema';
            } finally {
                this.loading = false;
            }
        },

        // Modificar configuración de asistencia "sistema" de una empresa o configuración de un área
        async editarConfiguracionSistema(nuevosDatos) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.put(`/empresa/opciones/api/editar-configuracion-sistema`, nuevosDatos);
                if (response.data.status === 'success') {
                    this.configuracionAsistencia = response.data.data;
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                this.error = error.message || 'Error al editar la configuración';
            } finally {
                this.loading = false;
            }
        },
    },
});
