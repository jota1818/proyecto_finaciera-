<!-- Modal para seleccionar encabezados -->
<div class="modal fade" id="encabezadosModal" tabindex="-1" aria-labelledby="encabezadosModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="encabezadosModalLabel">Seleccionar Encabezados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="encabezadosForm" method="post" action="reporte.php">
                    <input type="hidden" name="filtro" id="filtro">
                    <input type="hidden" name="mes" id="mesModal">
                    <input type="hidden" name="anio" id="anioModal">
                    <input type="hidden" name="fecha_inicio" id="fecha_inicio_modal">
                    <input type="hidden" name="fecha_fin" id="fecha_fin_modal">
                    <input type="hidden" name="filtroFecha" id="filtroFechaInput">

                    <div id="opcionesEncabezados"></div>
                    <button type="submit" class="btn btn-primary mt-3">Generar Reporte</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para configuración -->
<div class="modal fade" id="configuracionModal" tabindex="-1" aria-labelledby="configuracionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="configuracionModalLabel">Configuración de Encabezados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="configuracionForm">
                    <!-- Prejudicial -->
                    <div class="mb-3">
                        <label class="form-label">Encabezados Pre-judicial</label>
                        <div class="form-check" id="encabezadosPrejudicial">
                            <input type="checkbox" class="form-check-input" id="fechaPrejudicial" value="Fecha">
                            <label class="form-check-label" for="fechaPrejudicial">Fecha</label>
                        </div>
                        <div class="form-check" id="encabezadosPrejudicial">
                            <input type="checkbox" class="form-check-input" id="fechaClavePrejudicial" value="Fecha Clave">
                            <label class="form-check-label" for="fechaClavePrejudicial">Fecha Clave</label>
                        </div>
                        <div class="form-check" id="encabezadosPrejudicial">
                            <input type="checkbox" class="form-check-input" id="actoPrejudicial" value="Acto">
                            <label class="form-check-label" for="actoPrejudicial">Acto</label>
                        </div>
                        <div class="form-check" id="encabezadosPrejudicial">
                            <input type="checkbox" class="form-check-input" id="accionFechaClavePrejudicial" value="Acción en Fecha Clave">
                            <label class="form-check-label" for="accionFechaClavePrejudicial">Acción en Fecha Clave</label>
                        </div>
                        <div class="form-check" id="encabezadosPrejudicial">
                            <input type="checkbox" class="form-check-input" id="descripcionPrejudicial" value="Descripción">
                            <label class="form-check-label" for="descripcionPrejudicial">Descripción</label>
                        </div>
                        <div class="form-check" id="encabezadosPrejudicial">
                            <input type="checkbox" class="form-check-input" id="objetivoLogradoPrejudicial" value="Objetivo Logrado">
                            <label class="form-check-label" for="objetivoLogradoPrejudicial">Objetivo Logrado</label>
                        </div>
                    </div>

                    <!-- Judicial -->
                    <div class="mb-3">
                        <label class="form-label">Encabezados Judicial</label>
                        <div class="form-check" id="encabezadosJudicial">
                            <input type="checkbox" class="form-check-input" id="fechaJudicial" value="Fecha">
                            <label class="form-check-label" for="fechaJudicial">Fecha</label>
                        </div>
                        <div class="form-check" id="encabezadosJudicial">
                            <input type="checkbox" class="form-check-input" id="fechaClaveJudicial" value="Fecha Clave">
                            <label class="form-check-label" for="fechaClaveJudicial">Fecha Clave</label>
                        </div>
                        <div class="form-check" id="encabezadosJudicial">
                            <input type="checkbox" class="form-check-input" id="actoJudicial" value="Acto">
                            <label class="form-check-label" for="actoJudicial">Acto</label>
                        </div>
                        <div class="form-check" id="encabezadosJudicial">
                            <input type="checkbox" class="form-check-input" id="accionFechaClaveJudicial" value="Acción en Fecha Clave">
                            <label class="form-check-label" for="accionFechaClaveJudicial">Acción en Fecha Clave</label>
                        </div>
                        <div class="form-check" id="encabezadosJudicial">
                            <input type="checkbox" class="form-check-input" id="descripcionJudicial" value="Descripción">
                            <label class="form-check-label" for="descripcionJudicial">Descripción</label>
                        </div>
                    </div>

                    <!-- Sin Historial -->
                    <div class="mb-3">
                        <label class="form-label">Encabezados Sin Historial</label>
                        <div class="form-check" id="encabezadosSinHistorial">
                            <input type="checkbox" class="form-check-input" id="nombresSinHistorial" value="Nombres">
                            <label class="form-check-label" for="nombresSinHistorial">Nombres</label>
                        </div>
                        <div class="form-check" id="encabezadosSinHistorial">
                            <input type="checkbox" class="form-check-input" id="dniSinHistorial" value="DNI">
                            <label class="form-check-label" for="dniSinHistorial">DNI</label>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="guardarConfiguracion()">Guardar Configuración</button>
                </form>
            </div>
        </div>
    </div>
</div>