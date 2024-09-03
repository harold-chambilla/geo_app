<template>
  <div>
    <h2>Datos del Personal</h2>
    <form @submit.prevent="handleSubmit">
      <div class="form-row">
        <div class="inputContainer">
          <input type="text" v-model="nombre" class="input1" placeholder="Nombre" required />
          <p class="label">Nombre</p>
        </div>
        <div class="inputContainer">
          <input type="text" v-model="apellido" class="input1" placeholder="Apellido" required />
          <p class="label">Apellido</p>
        </div>
        <div class="inputContainer">
          <input type="text" v-model="dni" class="input1" placeholder="DNI" required />
          <p class="label">DNI</p>
        </div>
      </div>
      <div class="form-row">
        <div class="inputContainer">
          <input type="email" v-model="correoElectronico" class="input3 input1" placeholder="Correo electrónico" required />
          <p class="label">Correo electrónico</p>
        </div>
        <div class="inputContainer">
          <input type="date" v-model="fechaNacimiento" class="input2 input1" required />
          <p class="label2 label">Fecha de nacimiento</p>
        </div>
        <div class="inputContainer">
          <select v-model="area" class="input1" required>
            <option value="" disabled selected hidden></option>
            <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.nombre }}</option>
          </select>
          <p class="label">Área</p>
        </div>
      </div>
      <div class="form-row">
        <div class="inputContainer">
          <select v-model="puesto" class="input1" required>
            <option value="" disabled selected hidden></option>
            <option v-for="puesto in puestos" :key="puesto.id" :value="puesto.id">{{ puesto.nombre }}</option>
          </select>
          <p class="label">Puesto</p>
        </div>
        <div class="inputContainer">
          <select v-model="rol" class="input1" required>
            <option value="" disabled selected hidden></option>
            <option value="ROLE_SUPERADMIN">Super-Administrador</option>
            <option value="ROLE_ADMIN">Administrador</option> 
            <option value="ROLE_SUPERSUPERVISOR">Super-Supervisor</option>
            <option value="ROLE_SUPERVISOR">Supervisor</option>
            <option value="ROLE_COLABORADOR">Colaborador</option>
          </select>
          <p class="label">ROL</p>
        </div>
        <div class="inputContainer">
          <select v-model="sede" class="input1" required>
            <option value="" disabled selected hidden></option>
            <option v-for="sede in sedes" :key="sede.id" :value="sede.id">{{ sede.nombre }}</option>
          </select>
          <p class="label">Sede</p>
        </div>
      </div>
      <div class="linea-azul"></div>
      <div class="form-row">
        <div class="inputContainer">
          <input type="text" v-model="usuario" class="input1" placeholder="Usuario" required />
          <p class="label">Usuario</p>
        </div>
        <div class="inputContainer">
          <input type="password" v-model="contrasena" class="input1" placeholder="Contraseña" required />
          <p class="label">Contraseña</p>
        </div>
        <div class="inputContainer">
          <input type="password" v-model="repetirContrasena" class="input1" placeholder="Repetir Contraseña" required />
          <p class="label">Repetir Contraseña</p>
        </div>
      </div>
      <div class="inputContainer">
        <span class="checkboxLabel">
          <input type="checkbox" v-model="aceptaTerminos" id="terms" class="checkboxInput" required />
          Acepto los términos y condiciones
        </span>
      </div>
      <button type="submit" class="submitBtnA">Registrar</button>
    </form>

    <div v-if="error" class="alert alert-danger mt-3">
      {{ error }}
    </div>
    <div v-if="user" class="alert alert-success mt-3">
      Usuario creado exitosamente: {{ user.nombre }} {{ user.apellido }}
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { empleadosStore } from '../../../../store/empresa/empleados';

const nombre = ref('');
const apellido = ref('');
const dni = ref('');
const fechaNacimiento = ref('');
const correoElectronico = ref('');
const area = ref('');
const puesto = ref('');
const rol = ref('ROLE_COLABORADOR');
const sede = ref('');
const usuario = ref('');
const contrasena = ref('');
const repetirContrasena = ref('');
const aceptaTerminos = ref(false);

const datos = JSON.parse(document.getElementById('crear_empleado_local').dataset.contenido);
const areas = ref(datos.areas);
const sedes = ref(datos.sedes);
const puestos = ref([]);

watch(area, (newArea) => {
  const selectedArea = areas.value.find(a => a.id === newArea);
  puestos.value = selectedArea ? selectedArea.puestos : [];
});

const userStore = empleadosStore();

const handleSubmit = () => {
  if (contrasena.value !== repetirContrasena.value) {
    console.error('Las contraseñas no coinciden');
    return;
  }

  const userData = {
    nombre: nombre.value,
    apellido: apellido.value,
    dni: dni.value,
    fechaNacimiento: fechaNacimiento.value,
    correoElectronico: correoElectronico.value,
    area: area.value,
    puesto: puesto.value,
    rol: [rol.value],
    sede: sede.value,
    usuario: usuario.value,
    contrasena: contrasena.value,
    aceptaTerminos: aceptaTerminos.value,
  };

  userStore.createUser(userData);
};

const { user, error } = userStore;
</script>

<style scoped>
/* Puedes agregar estilos personalizados aquí */
</style>

