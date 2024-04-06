<template>
  <div>
   <!-- Encabezado con botón de retroceso -->
      <div class="row">
          <div class="col-md-12">
              <div class="header d-flex justify-content-center align-items-center">
                  <a href="#" class="back-link">
                      <span class="back-arrow">&lt;</span>
                  </a> 
                  <span class="text-dark">{{ salida.usuario }}</span>
              </div>
          </div>
      </div>

    <div class="container">
      <!-- Sección principal con imagen y texto -->
      <div class="row">
          <div class="col-md-12">
              <div class="image-container">
                <img  src="../../../ico/colaborador/asistencia/resultado/ico_puntualidad.png" width="80" height="75">
              </div>
              <div class="text-container">
                  <div class="text-box">
                      <span class="text" >{{ salida.estado }}</span>
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
                              <img  class="icono_label"  src="../../../ico/colaborador/asistencia/resultado/ico_calendario.png" width="40" height="40">
                              <label class="label_text" for="inp_fecha">{{ salida.fecha }}</label>
                          </span>
                          <span class="custom-ico input-group-prepend">
                              <img class="icono_label"  src="../../../ico/colaborador/asistencia/resultado/ico_h_marcacion.png" width="40" height="40">
                              <label class="label_text" for="inp_hora">{{ salida.hora }}</label>
                          </span>
                          <span class="custom-ico input-group-prepend">
                              <img class="icono_label" src="../../../ico/colaborador/asistencia/resultado/ico_puntualidad_02.png" width="40" height="40">
                              <label class="label_text inp_ubicacion" for="inp_ubicacion">{{ salida.estado }}</label>
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
                              <img class="icono_label" src="../../../ico/colaborador/asistencia/resultado/ico_usuario.png" width="40" height="40">
                              <label class="label_text" for="inp_usuario">{{ salida.usuario }}</label>
                          </span>
                          <span class="custom-ico input-group-prepend">
                              <img class="icono_label" src="../../../ico/colaborador/asistencia/resultado/ico_DNI.png" width="40" height="40">
                              <label class="label_text" for="inp_DNI">{{ salida.dni }}</label>
                          </span>
                          <span class="custom-ico input-group-prepend">
                              <img class="icono_label" src="../../../ico/colaborador/asistencia/resultado/ico_presencial.png" width="40" height="40">
                              <label class="label_text" for="inp_formato">{{ salida.modalidad }}</label>
                          </span>
                      </div>
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
  const salida = computed(() => store.SALIDA);
  
  </script>
  