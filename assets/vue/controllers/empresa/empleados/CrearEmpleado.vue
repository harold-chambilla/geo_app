<template>
    <div>
      <h2>Crear Usuario</h2>
      <form @submit.prevent="handleSubmit">
        <div class="mb-3">
          <label for="nombre" class="form-label">Nombres:</label>
          <input type="text" v-model="nombre" class="form-control" id="nombre" required />
        </div>
        <div class="mb-3">
          <label for="apellidos" class="form-label">Apellidos:</label>
          <input type="text" v-model="apellidos" class="form-control" id="apellidos" required />
        </div>
        <div class="mb-3">
          <label for="dni" class="form-label">DNI:</label>
          <input type="text" v-model="dni" class="form-control" id="dni" required />
        </div>
        <div class="mb-3">
          <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
          <input type="date" v-model="fechaNacimiento" class="form-control" id="fecha_nacimiento" required />
        </div>
        <div class="mb-3">
          <label for="correo" class="form-label">Correo Electrónico:</label>
          <input type="email" v-model="correoElectronico" class="form-control" id="correo" required />
        </div>
        <div class="mb-3">
          <label for="nombre_usuario" class="form-label">Nombre de Usuario:</label>
          <input type="text" v-model="nombreUsuario" class="form-control" id="nombre_usuario" required />
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Contraseña:</label>
          <input type="password" v-model="password" class="form-control" id="password" required />
        </div>
        <button type="submit" class="btn btn-primary">Crear Usuario</button>
      </form>
  
      <div v-if="error" class="alert alert-danger mt-3">
        {{ error }}
      </div>
      <div v-if="user" class="alert alert-success mt-3">
        Usuario creado exitosamente: {{ user.col_nombres }} {{ user.col_apellidos }}
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { administracionStore } from '../../../../store/empresa/administracion';
  
  // Referencias para los campos del formulario
  const nombre = ref('');
  const apellidos = ref('');
  const dni = ref('');
  const fechaNacimiento = ref('');
  const correoElectronico = ref('');
  const nombreUsuario = ref('');
  const password = ref('');
  
  // Obtener la instancia del store
  const userStore = administracionStore();
  
  // Manejar la sumisión del formulario
  const handleSubmit = () => {
    // Crear un FormData
    const userData = {
        col_nombres: nombre.value,
        col_apellidos: apellidos.value,
        col_dninit: dni.value,
        col_fechanacimiento: fechaNacimiento.value,
        col_correoelectronico: correoElectronico.value,
        col_nombreusuario: nombreUsuario.value,
        password: password.value,
    };
  
    console.log('vue-component', userData);
    // Llamar la acción del store para crear el usuario
    userStore.createUser(userData);
  };
  
  // Extraer datos del store para mostrar mensajes
  const { user, error } = userStore;
  </script>
  
  <style scoped>
  /* Puedes agregar estilos personalizados aquí */
  </style>
  