<template>
 
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="empresa-tab" data-bs-toggle="tab" data-bs-target="#registro" type="button" role="tab" aria-controls="registro" aria-selected="true">
                    Registro individual
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="personal-tab" data-bs-toggle="tab" data-bs-target="#excel" type="button" role="tab" aria-controls="excel" aria-selected="false">
                    Registro masivo
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="sistema-tab" data-bs-toggle="tab" data-bs-target="#sistema" type="button" role="tab" aria-controls="sistema" aria-selected="false">
                    Historial
                </button>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <!-- Empresa Tab -->
            <div class="tab-pane fade show active" id="registro" role="tabpanel" aria-labelledby="registro-tab">
            </div>

            <div class="tab-pane fade" id="excel" role="tabpanel" aria-labelledby="excel-tab">
            </div> 

            <div class="tab-pane fade" id="historial" role="tabpanel" aria-labelledby="historial-tab">
            </div>
        </div>
</template>
<script setup></script>
