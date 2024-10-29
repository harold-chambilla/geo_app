<template>
    <div class="container">
        <!-- Título de la sección -->
        <div class="d-flex align-items-center mt-4 mb-3">
            <i class="bi bi-bell-fill fs-3" style="color: #003366;"></i>           
            <h5 class="ms-2 mb-0 fw-bold">Notificaciones</h5>
        </div>

        <div class="row">
            <!-- Columna izquierda de switches -->
            <div class="col-md-6">
                <!-- Switch 1 -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="form-check-label" for="notificacion1">
                        Envío por correo de faltas y tardanzas diaria
                    </label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="notificacion1" checked>
                    </div>
                </div>

                <!-- Switch 2 -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="form-check-label" for="notificacion2">
                        Envío al colaborador por correo de permisos creados
                    </label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="notificacion2" checked>
                    </div>
                </div>
            </div>

            <!-- Columna derecha de switches -->
            <div class="col-md-6">
                <!-- Switch 3 -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="form-check-label" for="notificacion3">
                        Envío al colaborador por correo de vacaciones creadas
                    </label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="notificacion3" checked>
                    </div>
                </div>

                <!-- Switch 4 -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="form-check-label" for="notificacion4">
                        Envío al colaborador por correo el registro de su marcación de asistencia
                    </label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="notificacion4" checked>
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
