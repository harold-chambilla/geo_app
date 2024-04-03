<template>
    
     <!-- Encabezado con botón de retroceso -->
        <div class="row">
            <div class="col-md-12">
                <div class="header d-flex justify-content-center align-items-center">
                    <a href="#" class="back-link">
                        <span class="back-arrow">&lt;</span>
                    </a> 
                    <span class="text-dark">{{ entrada.usuario }}</span>
                </div>
            </div>
        </div>

      <div class="container">
        <!-- Sección principal con imagen y texto -->
        <div class="row">
            <div class="col-md-12">
                <div class="image-container">
                  <img  src="../../../ico/easy_control/colaborador/asistencia/resultado/ico_puntualidad.png" width="80" height="75">
                </div>
                <div class="text-container">
                    <div class="text-box">
                        <span class="text" >{{ entrada.estado }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <!-- Sección de información de puntualidad -->
                    <div class="text-container">
                        <div class="form-group">
                            <p class="Parrafo1">Información de la puntualidad</p>
                            <span class="custom-ico input-group-prepend">
                                <img  class="icono_label"  src="../../../ico/easy_control/colaborador/asistencia/resultado/ico_calendario.png" width="40" height="40">
                                <label class="label_text" for="inp_fecha">{{ entrada.fecha }}</label>
                            </span>
                            <span class="custom-ico input-group-prepend">
                                <img class="icono_label"  src="../../../ico/easy_control/colaborador/asistencia/resultado/ico_h_marcacion.png" width="40" height="40">
                                <label class="label_text" for="inp_hora">{{ entrada.hora }}</label>
                            </span>
                            <span class="custom-ico input-group-prepend">
                                <img class="icono_label" src="../../../ico/easy_control/colaborador/asistencia/resultado/ico_puntualidad_02.png" width="40" height="40">
                                <label class="label_text inp_ubicacion" for="inp_ubicacion">{{ entrada.estado }}</label>
                            </span>
                        </div>
                    </div>
                </div>
            
                <div class="col-md-6">
                    <!-- Datos personales -->
                    <div class="text-container">
                        <div class="form-group">
                            <p class="Parrafo1">Datos personales</p>
                            <span class="custom-ico input-group-prepend">
                                <img class="icono_label" src="../../../ico/easy_control/colaborador/asistencia/resultado/ico_usuario.png" width="40" height="40">
                                <label class="label_text" for="inp_usuario">{{ entrada.usuario }}</label>
                            </span>
                            <span class="custom-ico input-group-prepend">
                                <img class="icono_label" src="../../../ico/easy_control/colaborador/asistencia/resultado/ico_DNI.png" width="40" height="40">
                                <label class="label_text" for="inp_DNI">{{ entrada.dni }}</label>
                            </span>
                            <span class="custom-ico input-group-prepend">
                                <img class="icono_label" src="../../../ico/easy_control/colaborador/asistencia/resultado/ico_presencial.png" width="40" height="40">
                                <label class="label_text" for="inp_formato">{{ entrada.modalidad }}</label>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>   
  </template>
  
  <script setup>
  import { computed } from 'vue';
  import { asistenciaStore } from '../../../store/asistencia.js';
  
  const store = asistenciaStore();
  const entrada = computed(() => store.ENTRADA);

  </script>


  