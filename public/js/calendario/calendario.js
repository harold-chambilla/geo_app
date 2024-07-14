// public/js/calendario.js

const monthNames = [
    "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
    "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
];

let currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();

function updateCalendar() {
    document.getElementById('currentMonth').textContent = `${monthNames[currentMonth]} ${currentYear}`;

    // Obtener el primer día y la cantidad de días del mes actual
    let firstDay = new Date(currentYear, currentMonth, 1).getDay();
    let daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

    // Calcular el primer día de la semana anterior
    let prevMonthLastDay = new Date(currentYear, currentMonth, 0).getDate();
    let startPrevMonth = prevMonthLastDay - firstDay + 1;

    // Actualizar el calendario con los días del mes actual y del mes anterior
    let semanas = document.querySelectorAll('.calendar-table');
    let numeroDia = 1;

    semanas.forEach(semana => {
        let diasSemana = semana.querySelectorAll('td[id^="semana"]');
        diasSemana.forEach((td, index) => {
            if (index < firstDay) {
                // Mostrar días del mes anterior
                td.textContent = startPrevMonth++;
                td.classList.add('prev-month');
            } else if (numeroDia <= daysInMonth) {
                // Mostrar días del mes actual
                td.textContent = numeroDia++;
                td.classList.remove('prev-month');
            } else {
                // Limpiar días restantes
                td.textContent = '';
            }
        });
        firstDay = 0; // Reiniciar firstDay para las semanas siguientes
    });
}

document.getElementById('prevMonth').addEventListener('click', function() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    updateCalendar();
});

document.getElementById('nextMonth').addEventListener('click', function() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    updateCalendar();
});

// Inicializar el calendario al cargar la página
updateCalendar();

function agregarNombre() {
    const nombre = document.getElementById('nombreInput').value.trim();
    if (nombre !== "") {
        const personalCeldas = document.querySelectorAll('td[id^="personal"]');
        personalCeldas.forEach(celda => {
            // Añadir espacio para el nombre debajo de "PERSONAL"
            const filaPersonal = celda.parentNode;
            filaPersonal.insertAdjacentHTML('afterend', `
                <tr class="personal">
                    <td colspan="2" class="personal">${nombre}</td>
                    <td class="domingo libre">Libre</td>
                    <td class="lunes libre">Libre</td>
                    <td class="martes libre">Libre</td>
                    <td class="miercoles libre">Libre</td>
                    <td class="jueves libre">Libre</td>
                    <td class="viernes libre">Libre</td>
                    <td class="sabado libre">Libre</td>
                </tr>
            `);
        });
        document.getElementById('nombreInput').value = ''; // Limpiar el input después de agregar el nombre
        agregarEventoLibre();
    }
}

document.getElementById('addNombre').addEventListener('click', agregarNombre);

// Lógica para mostrar el modal para todos los empleados
function showModalForAllEmployees() {
    // Verificar si hay algún personal agregado
    const personal = document.querySelectorAll('.personal');
    if (personal.length === 0) {
        // Si no hay personal, mostrar una alerta
        alert("No se ha agregado ningún personal.");
        return; // Salir de la función
    }

    // Configurar el encabezado y subencabezado del modal
    modalHeader.textContent = `Horas de trabajo para todos los empleados - ${monthNames[currentMonth]} ${currentYear}`;
    modalSubheader.textContent = "";

    // Mostrar el modal
    modal.style.display = "block";
}

// Modifica esta función para que al hacer clic en el nombre del mes, se abra el modal para ingresar las horas de todos los empleados en el día seleccionado
document.getElementById('currentMonth').addEventListener('click', () => {
    if (hayPersonalAgregado()) {
        showModalForAllEmployees();
    } else {
        alert("No se ha agregado ningún personal.");
    }
});

// Función para verificar si hay personal agregado
function hayPersonalAgregado() {
    const personal = document.querySelectorAll('.personal');
    return personal.length > 0;
}

// Modal Logic
const modal = document.getElementById("myModal");
const modalHeader = document.getElementById("modalHeader");
const modalSubheader = document.getElementById("modalSubheader");
const ingreso = document.getElementById("ingreso");
const salida = document.getElementById("salida");
const presencial = document.getElementById("presencial");
const remoto = document.getElementById("remoto");
const aplicarATodo = document.getElementById("aplicarATodo");
const btnCancel = document.querySelector(".btn-cancel");
const btnAccept = document.querySelector(".btn-accept");

// Cerrar el modal haciendo clic en la X
document.querySelector(".close").addEventListener('click', () => {
    modal.style.display = "none";
});

// Cerrar el modal haciendo clic fuera del modal
window.addEventListener('click', (event) => {
    if (event.target === modal) {
        modal.style.display = "none";
    }
});

// Lógica para agregar evento libre
function agregarEventoLibre() {
    const celdasLibres = document.querySelectorAll('.libre');
    celdasLibres.forEach(celda => {
        celda.addEventListener('click', () => {
            modalHeader.textContent = `Ingresar horario para ${celda.parentElement.querySelector('.personal').textContent} el ${celda.classList[0]}`;
            modalSubheader.textContent = `${monthNames[currentMonth]} ${currentYear}`;

            // Muestra el modal
            modal.style.display = "block";

            // Lógica para aplicar a todo el personal
            aplicarATodo.addEventListener('change', () => {
                if (aplicarATodo.checked) {
                    ingreso.value = '';
                    salida.value = '';
                }
            });

            // Lógica para el botón de aceptar
            btnAccept.addEventListener('click', () => {
                // Aquí puedes implementar la lógica para guardar los datos del modal
                modal.style.display = "none";
            });

            // Lógica para el botón de cancelar
            btnCancel.addEventListener('click', () => {
                modal.style.display = "none";
            });
        });
    });
}

// Inicializar la lógica para agregar evento libre
agregarEventoLibre();
