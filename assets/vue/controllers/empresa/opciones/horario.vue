<template>
    <div class="container">
        <!-- Título de la sección -->
        <div class="d-flex align-items-center mt-4 mb-3">
            <i class="bi bi-clock-fill fs-3" style="color: #003366;"></i>                  
            <h5 class="ms-2 mb-0 fw-bold">Horario</h5>
        </div>

        <div class="row g-3">
            <!-- Switch para Área -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label mb-0" for="areaSwitch">Área</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="areaSwitch" checked>
                    </div>
                </div>
            </div>

            <!-- Switch para Puesto -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label mb-0" for="puestoSwitch">Puesto</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="puestoSwitch" checked>
                    </div>
                </div>
            </div>

            <!-- Switch para Modalidad de trabajo total -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label mb-0" for="modalidadSwitch">Modalidad de trabajo total</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="modalidadSwitch" checked>
                    </div>
                </div>
            </div>

            <!-- Switch para Predeterminar horario -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label mb-0" for="horarioSwitch">Predeterminar horario</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="horarioSwitch" checked>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón de guardar -->
        <div class="d-flex justify-content-center mt-4">
            <button class="btn btn-primary">Guardar</button>
        </div>
    </div>
</template>
<script setup></script>
