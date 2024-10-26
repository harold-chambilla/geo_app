<template>
<div class="container">
    <!-- Título con icono -->
    <div class="d-flex align-items-center mt-4 mb-3">
        <i class="bi bi-briefcase-fill" style="font-size: 2rem; color: #003366; text-shadow: 1px 1px 2px #001a33;"></i>
        <h5 class="ms-2 mb-0 fw-bold">Área y puesto</h5> <!-- Negrita y color oscuro -->
    </div>

    <!-- Formulario para Área -->
    <div class="row">
        <div class="col-md-6">
            <label for="area" class="form-label fw-semibold">Área</label> <!-- Etiqueta en negrita y color oscuro -->
            <input type="text" class="form-control mb-3" id="area" placeholder="Escribe el área...">
            <button class="btn btn-primary mb-3">+ Agregar</button> <!-- Botón azul oscuro -->
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Áreas</label> <!-- Etiqueta en negrita y color oscuro -->
            <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Finanzas
                    <button class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i>
                    </button>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    RR.HH
                    <button class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i>
                    </button>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Contabilidad
                    <button class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i>
                    </button>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Sistemas
                    <button class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i>
                    </button>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Soporte
                    <button class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div> 

    <!-- Formulario para Puesto -->
    <div class="row">
        <div class="col-md-6">
            <label for="select-area" class="form-label fw-semibold">Seleccionar Área</label> <!-- Etiqueta en negrita y color oscuro -->
            <select id="select-area" class="form-select mb-3">
                <option selected>Selecciona un área</option>
                <option value="1">Finanzas</option>
                <option value="2">RR.HH</option>
                <option value="3">Contabilidad</option>
                <option value="4">Sistemas</option>
                <option value="5">Soporte</option>
            </select>

            <label for="puesto" class="form-label fw-semibold">Puesto</label> <!-- Etiqueta en negrita y color oscuro -->
            <input type="text" class="form-control mb-3" id="puesto" placeholder="Escribe el puesto...">
            <button class="btn btn-primary mb-3">+ Agregar</button> <!-- Botón azul oscuro -->
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Puestos</label> <!-- Etiqueta en negrita y color oscuro -->
            <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Puesto 1
                    <button class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i>
                    </button>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Puesto 2
                    <button class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i>
                    </button>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Puesto 3
                    <button class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Botón Guardar -->
    <div class="d-flex justify-content-center mt-4">
        <button class="btn btn-primary">Guardar</button> <!-- Botón azul oscuro -->
    </div>
</div>

</template>
<script setup></script>
