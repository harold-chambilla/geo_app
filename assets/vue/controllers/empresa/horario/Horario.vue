<template>
  <div class="CTN-principal">
    <div class="calendar-header">
      <button class="button-blanco" @click="prevMonth">Anterior</button>
      <span class="mes">{{ currentMonthName }} {{ currentYear }}</span>
      <button class="button-blanco" @click="nextMonth">Siguiente</button>
    </div>

    <div class="container-submenu">
      <div class="dropdown">
        <select>
          <option value="area">Area</option>
          <option value="option1">Option 1</option>
          <option value="option2">Option 2</option>
        </select>
      </div>
      <div class="dropdown">
        <select>
          <option value="puesto">Puesto</option>
          <option value="option1">Option 1</option>
          <option value="option2">Option 2</option>
        </select>
      </div>
      <input type="text" v-model="nombreSemana" placeholder="Nombre de la semana">
      <button @click="agregarNombre">Agregar Nombre</button>
    </div>

    <div class="calendar-body">
      <div class="table-container">
        <table class="calendar-table" v-for="(semana, index) in semanas" :key="index">
          <tbody>
            <tr>
              <td class="semana" colspan="2"></td>
              <td class="semana domingo">Dom</td>
              <td class="semana lunes">Lun</td>
              <td class="semana martes">Mar</td>
              <td class="semana miercoles">Mié</td>
              <td class="semana jueves">Jue</td>
              <td class="semana viernes">Vie</td>
              <td class="semana sabado">Sáb</td>
            </tr>
            <tr class="personal">
              <td colspan="2">Semana {{ index + 1 }}</td>
              <td v-for="(dia, diaIndex) in semana" :key="diaIndex" class="libre" @click="abrirModalDia(index + 1, diaIndex)">
                {{ dia }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal" @click.self="closeModal">
      <div class="modal-content">
        <span class="close" @click="closeModal">&times;</span>
        <h2>{{ modalHeader }}</h2>
        <h3>{{ modalSubheader }}</h3>
        <div>
          <label for="ingreso">Hora de Ingreso:</label>
          <input type="time" v-model="ingreso">
        </div>
        <div>
          <label for="salida">Hora de Salida:</label>
          <input type="time" v-model="salida">
        </div>
        <div>
          <label>
            <input type="radio" value="Presencial" v-model="modalidad"> Presencial
          </label>
          <label>
            <input type="radio" value="Remoto" v-model="modalidad"> Remoto
          </label>
        </div>
        <div>
          <label>
            <input type="checkbox" v-model="aplicarATodo"> Aplicar a todo el personal
          </label>
        </div>
        <div>
          <button @click="closeModal">Cancelar</button>
          <button @click="guardarDatos">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      currentMonth: new Date().getMonth(),
      currentYear: new Date().getFullYear(),
      semanas: Array(5).fill(Array(7).fill('')),
      nombreSemana: '',
      showModal: false,
      modalHeader: '',
      modalSubheader: '',
      ingreso: '',
      salida: '',
      modalidad: '',
      aplicarATodo: false,
    };
  },
  computed: {
    currentMonthName() {
      const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
      return monthNames[this.currentMonth];
    }
  },
  methods: {
    updateCalendar() {
      const firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
      const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
      let startPrevMonth = new Date(this.currentYear, this.currentMonth, 0).getDate() - firstDay + 1;
      let numeroDia = 1;
      this.semanas = this.semanas.map((semana) => {
        return semana.map((dia, index) => {
          if (index < firstDay) return startPrevMonth++;
          else if (numeroDia <= daysInMonth) return numeroDia++;
          return '';
        });
      });
    },
    prevMonth() {
      this.currentMonth--;
      if (this.currentMonth < 0) {
        this.currentMonth = 11;
        this.currentYear--;
      }
      this.updateCalendar();
    },
    nextMonth() {
      this.currentMonth++;
      if (this.currentMonth > 11) {
        this.currentMonth = 0;
        this.currentYear++;
      }
      this.updateCalendar();
    },
    agregarNombre() {
      if (this.nombreSemana) {
        this.nombreSemana = '';
      }
    },
    openModal() {
      if (this.nombreSemana) this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
    },
    abrirModalDia(semana, dia) {
      this.modalHeader = `Horario para Semana ${semana}, Día ${dia}`;
      this.modalSubheader = `${this.currentMonthName} ${this.currentYear}`;
      this.showModal = true;
    },
    guardarDatos() {
      this.showModal = false;
    }
  },
  mounted() {
    this.updateCalendar();
  }
};
</script>

<style scoped>
.CNT-principal {
  padding: 20px;
}

.calendar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.button-blanco {
  padding: 10px 20px;
  background-color: #fff;
  border: 1px solid #ddd;
  cursor: pointer;
}

.mes {
  font-size: 1.5em;
  font-weight: bold;
}

.container-submenu {
  display: flex;
  justify-content: space-around;
  margin-bottom: 20px;
}

.calendar-body {
  display: flex;
  justify-content: center;
}

.table-container {
  display: inline-block;
  margin-right: 10px;
}

.calendar-table {
  width: 100%;
  border-collapse: collapse;
}

.semana {
  background-color: #f4f4f4;
  text-align: center;
  font-weight: bold;
}

.libre {
  text-align: center;
  cursor: pointer;
  background-color: #e7f4ff;
}

.modal {
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
  background-color: #fefefe;
  margin: 15% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
</style>

