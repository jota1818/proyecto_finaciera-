<?php
require "../conexion_db/connection.php";

// Inicializar variables para el mes y año
$mes = isset($_POST['mes']) ? $_POST['mes'] : date('m');
$anio = isset($_POST['anio']) ? $_POST['anio'] : date('Y');
$filtro = isset($_POST['filtro']) ? $_POST['filtro'] : 'todos'; // Nuevo filtro
$encabezados_prejudicial = isset($_POST['encabezados_prejudicial']) ? $_POST['encabezados_prejudicial'] : []; // Encabezados seleccionados para pre-judicial
$encabezados_judicial = isset($_POST['encabezados_judicial']) ? $_POST['encabezados_judicial'] : []; // Encabezados seleccionados para judicial
$encabezados_sin_historial = isset($_POST['encabezados_sin_historial']) ? $_POST['encabezados_sin_historial'] : []; // Encabezados seleccionados para sin historial

// Verificar si el formulario de encabezados ha sido enviado
$reporte_generado = isset($_POST['filtro']);

if ($reporte_generado) {
    // Convertir el mes y año a un rango de fechas
    $fecha_inicio = $anio . '-' . $mes . '-01';
    $fecha_fin = date('Y-m-t', strtotime($fecha_inicio));

    // Consulta para obtener los clientes con historial pre-judicial del mes seleccionado
    $sql_prejudicial = "
        SELECT c.*, p.*
        FROM etapa_prejudicial p
        JOIN clientes c ON p.id_cliente = c.id_cliente
        WHERE p.fecha_acto BETWEEN '$fecha_inicio' AND '$fecha_fin'
        ORDER BY c.nombre, c.apellidos, p.fecha_acto
    ";
    $result_prejudicial = $conn->query($sql_prejudicial);

    // Consulta para obtener los clientes con historial judicial del mes seleccionado
    $sql_judicial = "
        SELECT c.*, j.*
        FROM etapa_judicial j
        JOIN clientes c ON j.id_cliente = c.id_cliente
        WHERE j.fecha_judicial BETWEEN '$fecha_inicio' AND '$fecha_fin'
        ORDER BY c.nombre, c.apellidos, j.fecha_judicial
    ";
    $result_judicial = $conn->query($sql_judicial);

    // Consulta para obtener todos los clientes
    $sql_clientes = "SELECT * FROM clientes ORDER BY nombre, apellidos";
    $result_clientes = $conn->query($sql_clientes);

    // Obtener la lista de clientes con historial
    $clientes_con_historial = [];
    while ($row = $result_prejudicial->fetch_assoc()) {
        $clientes_con_historial[$row['id_cliente']] = true;
    }
    while ($row = $result_judicial->fetch_assoc()) {
        $clientes_con_historial[$row['id_cliente']] = true;
    }

    // Obtener la lista de clientes sin historial
    $clientes_sin_historial = [];
    while ($row = $result_clientes->fetch_assoc()) {
        if (!isset($clientes_con_historial[$row['id_cliente']])) {
            $clientes_sin_historial[] = $row;
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Reporte de Historiales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
    <div class="container-fluid mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-center w-100">Reporte de Historiales -
                <?php
                $meses = [
                    '01' => 'Enero',
                    '02' => 'Febrero',
                    '03' => 'Marzo',
                    '04' => 'Abril',
                    '05' => 'Mayo',
                    '06' => 'Junio',
                    '07' => 'Julio',
                    '08' => 'Agosto',
                    '09' => 'Septiembre',
                    '10' => 'Octubre',
                    '11' => 'Noviembre',
                    '12' => 'Diciembre'
                ];
                echo $meses[$mes] . ' ' . $anio;
                ?>
            </h2>
            <button type="button" class="btn btn-danger btn-salir" onclick="window.location.href='../registro_cliente/index.php'">Salir</button>
        </div>

        <!-- Formulario para seleccionar mes y año -->
        <form id="reporteForm" method="post" action="" class="d-flex justify-content-center mb-4">
            <div class="me-2">
                <label for="mes">Mes:</label>
                <select id="mes" name="mes" class="form-select form-select-sm" required>
                    <?php
                    foreach ($meses as $value => $label) {
                        echo "<option value='$value' " . ($value == $mes ? 'selected' : '') . ">$label</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="me-2">
                <label for="anio">Año:</label>
                <input type="number" id="anio" name="anio" class="form-control form-control-sm" value="<?php echo $anio; ?>" required>
            </div>
        </form>

        <!-- Botones de filtro -->
        <div class="d-flex justify-content-center mb-4">
            <button type="button" class="btn btn-secondary me-2" onclick="abrirModal('con_historial')">Clientes con Historial</button>
            <button type="button" class="btn btn-secondary me-2" onclick="abrirModal('sin_historial')">Clientes sin Historial</button>
            <button type="button" class="btn btn-secondary me-2" onclick="abrirModal('todos')">Clientes con y sin Historial</button>
            <button type="button" class="btn btn-primary" onclick="abrirConfiguracion()">Configuración</button>
        </div>

        <!-- Modal para seleccionar encabezados -->
        <div class="modal fade" id="encabezadosModal" tabindex="-1" aria-labelledby="encabezadosModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="encabezadosModalLabel">Seleccionar Encabezados</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="encabezadosForm" method="post" action="">
                            <input type="hidden" name="filtro" id="filtro">
                            <input type="hidden" name="mes" id="mesModal" value="<?php echo $mes; ?>">
                            <input type="hidden" name="anio" id="anioModal" value="<?php echo $anio; ?>">
                            <div id="opcionesEncabezados">
                                <!-- Opciones de encabezados se insertarán aquí dinámicamente -->
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Generar Reporte</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para configuración de encabezados -->
        <div class="modal fade" id="configuracionModal" tabindex="-1" aria-labelledby="configuracionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="configuracionModalLabel">Configuración de Encabezados</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="configuracionForm">
                            <div class="mb-3">
                                <label class="form-label">Encabezados Pre-judicial por defecto</label>
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
                            <div class="mb-3">
                                <label class="form-label">Encabezados Judicial por defecto</label>
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
                            <div class="mb-3">
                                <label class="form-label">Encabezados Sin Historial por defecto</label>
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

        <!-- Historiales de Clientes -->
        <?php if ($reporte_generado): ?>
            <?php
            $clientes_prejudicial = [];
            $result_prejudicial->data_seek(0); // Reiniciar el puntero del resultado
            while ($row = $result_prejudicial->fetch_assoc()) {
                $clientes_prejudicial[$row['id_cliente']][] = $row;
            }

            $clientes_judicial = [];
            $result_judicial->data_seek(0); // Reiniciar el puntero del resultado
            while ($row = $result_judicial->fetch_assoc()) {
                $clientes_judicial[$row['id_cliente']][] = $row;
            }

            if ($filtro === 'con_historial') {
                // Mostrar solo clientes con historial
                foreach ($clientes_prejudicial as $id_cliente => $prejudiciales) {
                    $cliente = $prejudiciales[0];
                    $judiciales = $clientes_judicial[$id_cliente] ?? [];
                    mostrarCliente($cliente, $prejudiciales, $judiciales, $encabezados_prejudicial, $encabezados_judicial);
                }
            } elseif ($filtro === 'sin_historial') {
                // Mostrar solo clientes sin historial
                mostrarClientesSinHistorial($clientes_sin_historial, $encabezados_sin_historial);
            } else {
                // Mostrar todos los clientes
                foreach ($clientes_prejudicial as $id_cliente => $prejudiciales) {
                    $cliente = $prejudiciales[0];
                    $judiciales = $clientes_judicial[$id_cliente] ?? [];
                    mostrarCliente($cliente, $prejudiciales, $judiciales, $encabezados_prejudicial, $encabezados_judicial);
                }
                mostrarClientesSinHistorial($clientes_sin_historial, $encabezados_sin_historial);
            }
            ?>
            <!-- Botón de descarga de reporte -->
            <form id="descargarReporteForm" method="post" action="generar_pdf.php" target="_blank">
                <input type="hidden" name="mes" value="<?php echo $mes; ?>">
                <input type="hidden" name="anio" value="<?php echo $anio; ?>">
                <input type="hidden" name="filtro" value="<?php echo $filtro; ?>">
                <input type="hidden" name="encabezados_prejudicial" value="<?php echo implode(',', $encabezados_prejudicial); ?>">
                <input type="hidden" name="encabezados_judicial" value="<?php echo implode(',', $encabezados_judicial); ?>">
                <input type="hidden" name="encabezados_sin_historial" value="<?php echo implode(',', $encabezados_sin_historial); ?>">
                <button type="submit" class="btn btn-success mt-3">Descargar Reporte</button>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function abrirModal(filtro) {
            document.getElementById('filtro').value = filtro;
            document.getElementById('mesModal').value = document.getElementById('mes').value;
            document.getElementById('anioModal').value = document.getElementById('anio').value;

            const opcionesEncabezados = document.getElementById('opcionesEncabezados');
            opcionesEncabezados.innerHTML = ''; // Limpiar opciones anteriores

            const encabezadosPrejudicial = JSON.parse(localStorage.getItem('encabezadosPrejudicial')) || [];
            const encabezadosJudicial = JSON.parse(localStorage.getItem('encabezadosJudicial')) || [];
            const encabezadosSinHistorial = JSON.parse(localStorage.getItem('encabezadosSinHistorial')) || [];

            if (filtro === 'con_historial') {
                // Opciones para clientes con historial
                agregarOpcionEncabezado(opcionesEncabezados, 'Opción Pre-judicial', ['Fecha', 'Fecha Clave', 'Acto', 'Acción en Fecha Clave', 'Descripción', 'Objetivo Logrado'], 'encabezados_prejudicial', encabezadosPrejudicial);
                agregarOpcionEncabezado(opcionesEncabezados, 'Opción Judicial', ['Fecha', 'Fecha Clave', 'Acto', 'Acción en Fecha Clave', 'Descripción'], 'encabezados_judicial', encabezadosJudicial);
            } else if (filtro === 'sin_historial') {
                // Opciones para clientes sin historial
                agregarOpcionEncabezado(opcionesEncabezados, 'Opción sin historial', ['Nombres', 'DNI'], 'encabezados_sin_historial', encabezadosSinHistorial);
            } else {
                // Opciones para clientes con y sin historial
                agregarOpcionEncabezado(opcionesEncabezados, 'Opción Pre-judicial', ['Fecha', 'Fecha Clave', 'Acto', 'Acción en Fecha Clave', 'Descripción', 'Objetivo Logrado'], 'encabezados_prejudicial', encabezadosPrejudicial);
                agregarOpcionEncabezado(opcionesEncabezados, 'Opción Judicial', ['Fecha', 'Fecha Clave', 'Acto', 'Acción en Fecha Clave', 'Descripción'], 'encabezados_judicial', encabezadosJudicial);
                agregarOpcionEncabezado(opcionesEncabezados, 'Opción sin historial', ['Nombres', 'DNI'], 'encabezados_sin_historial', encabezadosSinHistorial);
            }

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('encabezadosModal'));
            modal.show();
        }

        function agregarOpcionEncabezado(contenedor, titulo, encabezados, nombreCampo, opcionesPorDefecto) {
            const opcionDiv = document.createElement('div');
            opcionDiv.classList.add('mb-3');

            const tituloLabel = document.createElement('label');
            tituloLabel.classList.add('form-label');
            tituloLabel.textContent = titulo;
            opcionDiv.appendChild(tituloLabel);

            encabezados.forEach(encabezado => {
                const checkboxDiv = document.createElement('div');
                checkboxDiv.classList.add('form-check');

                const checkboxInput = document.createElement('input');
                checkboxInput.type = 'checkbox';
                checkboxInput.name = nombreCampo + '[]';
                checkboxInput.value = encabezado;
                checkboxInput.classList.add('form-check-input');
                checkboxInput.checked = opcionesPorDefecto.includes(encabezado); // Marcar si está en las opciones por defecto
                checkboxDiv.appendChild(checkboxInput);

                const checkboxLabel = document.createElement('label');
                checkboxLabel.classList.add('form-check-label');
                checkboxLabel.textContent = encabezado;
                checkboxDiv.appendChild(checkboxLabel);

                opcionDiv.appendChild(checkboxDiv);
            });

            contenedor.appendChild(opcionDiv);
        }

        function abrirConfiguracion() {
            const modal = new bootstrap.Modal(document.getElementById('configuracionModal'));
            modal.show();
        }

        function guardarConfiguracion() {
            // Obtener los encabezados seleccionados
            const encabezadosPrejudicial = Array.from(document.querySelectorAll('#encabezadosPrejudicial input[type="checkbox"]:checked'))
                .map(checkbox => checkbox.value);
            const encabezadosJudicial = Array.from(document.querySelectorAll('#encabezadosJudicial input[type="checkbox"]:checked'))
                .map(checkbox => checkbox.value);
            const encabezadosSinHistorial = Array.from(document.querySelectorAll('#encabezadosSinHistorial input[type="checkbox"]:checked'))
                .map(checkbox => checkbox.value);

            // Guardar la configuración en el localStorage
            localStorage.setItem('encabezadosPrejudicial', JSON.stringify(encabezadosPrejudicial));
            localStorage.setItem('encabezadosJudicial', JSON.stringify(encabezadosJudicial));
            localStorage.setItem('encabezadosSinHistorial', JSON.stringify(encabezadosSinHistorial));

            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('configuracionModal'));
            modal.hide();
        }

        // Cargar la configuración guardada al cargar la página
        window.addEventListener('load', function() {
            const encabezadosPrejudicial = JSON.parse(localStorage.getItem('encabezadosPrejudicial')) || [];
            const encabezadosJudicial = JSON.parse(localStorage.getItem('encabezadosJudicial')) || [];
            const encabezadosSinHistorial = JSON.parse(localStorage.getItem('encabezadosSinHistorial')) || [];

            // Marcar los checkboxes según la configuración guardada
            encabezadosPrejudicial.forEach(encabezado => {
                document.querySelector(`#encabezadosPrejudicial input[value="${encabezado}"]`).checked = true;
            });
            encabezadosJudicial.forEach(encabezado => {
                document.querySelector(`#encabezadosJudicial input[value="${encabezado}"]`).checked = true;
            });
            encabezadosSinHistorial.forEach(encabezado => {
                document.querySelector(`#encabezadosSinHistorial input[value="${encabezado}"]`).checked = true;
            });
        });
    </script>
</body>

</html>

<?php
function mostrarCliente($cliente, $prejudiciales, $judiciales, $encabezados_prejudicial, $encabezados_judicial) {
    ?>
    <div class="client-box">
        <div class="client-header">
            <h4><?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']); ?></h4>
        </div>
        <div class="client-body">
            <?php if (!empty($prejudiciales) && (in_array('Fecha', $encabezados_prejudicial) || in_array('Fecha Clave', $encabezados_prejudicial) || in_array('Acto', $encabezados_prejudicial) || in_array('Acción en Fecha Clave', $encabezados_prejudicial) || in_array('Descripción', $encabezados_prejudicial) || in_array('Objetivo Logrado', $encabezados_prejudicial))) : ?>
                <h5>Etapa Pre-Judicial</h5>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <?php if (in_array('Fecha', $encabezados_prejudicial)) : ?>
                                <th>Fecha</th>
                            <?php endif; ?>
                            <?php if (in_array('Fecha Clave', $encabezados_prejudicial)) : ?>
                                <th>Fecha Clave</th>
                            <?php endif; ?>
                            <?php if (in_array('Acto', $encabezados_prejudicial)) : ?>
                                <th>Acto</th>
                            <?php endif; ?>
                            <?php if (in_array('Acción en Fecha Clave', $encabezados_prejudicial)) : ?>
                                <th>Acción en Fecha Clave</th>
                            <?php endif; ?>
                            <?php if (in_array('Descripción', $encabezados_prejudicial)) : ?>
                                <th>Descripción</th>
                            <?php endif; ?>
                            <?php if (in_array('Objetivo Logrado', $encabezados_prejudicial)) : ?>
                                <th>Objetivo Logrado</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prejudiciales as $prejudicial) : ?>
                            <tr>
                                <?php if (in_array('Fecha', $encabezados_prejudicial)) : ?>
                                    <td><?php echo htmlspecialchars($prejudicial['fecha_acto']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Fecha Clave', $encabezados_prejudicial)) : ?>
                                    <td><?php echo htmlspecialchars($prejudicial['fecha_clave']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Acto', $encabezados_prejudicial)) : ?>
                                    <td><?php echo htmlspecialchars($prejudicial['acto']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Acción en Fecha Clave', $encabezados_prejudicial)) : ?>
                                    <td><?php echo htmlspecialchars($prejudicial['accion_fecha_clave']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Descripción', $encabezados_prejudicial)) : ?>
                                    <td><?php echo htmlspecialchars($prejudicial['descripcion']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Objetivo Logrado', $encabezados_prejudicial)) : ?>
                                    <td><?php echo htmlspecialchars($prejudicial['objetivo_logrado']); ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if (!empty($judiciales) && (in_array('Fecha', $encabezados_judicial) || in_array('Fecha Clave', $encabezados_judicial) || in_array('Acto', $encabezados_judicial) || in_array('Acción en Fecha Clave', $encabezados_judicial) || in_array('Descripción', $encabezados_judicial))) : ?>
                <h5>Etapa Judicial</h5>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <?php if (in_array('Fecha', $encabezados_judicial)) : ?>
                                <th>Fecha</th>
                            <?php endif; ?>
                            <?php if (in_array('Fecha Clave', $encabezados_judicial)) : ?>
                                <th>Fecha Clave</th>
                            <?php endif; ?>
                            <?php if (in_array('Acto', $encabezados_judicial)) : ?>
                                <th>Acto</th>
                            <?php endif; ?>
                            <?php if (in_array('Acción en Fecha Clave', $encabezados_judicial)) : ?>
                                <th>Acción en Fecha Clave</th>
                            <?php endif; ?>
                            <?php if (in_array('Descripción', $encabezados_judicial)) : ?>
                                <th>Descripción</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($judiciales as $judicial) : ?>
                            <tr>
                                <?php if (in_array('Fecha', $encabezados_judicial)) : ?>
                                    <td><?php echo htmlspecialchars($judicial['fecha_judicial']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Fecha Clave', $encabezados_judicial)) : ?>
                                    <td><?php echo htmlspecialchars($judicial['fecha_clave_judicial']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Acto', $encabezados_judicial)) : ?>
                                    <td><?php echo htmlspecialchars($judicial['acto_judicial']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Acción en Fecha Clave', $encabezados_judicial)) : ?>
                                    <td><?php echo htmlspecialchars($judicial['accion_en_fecha_clave']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Descripción', $encabezados_judicial)) : ?>
                                    <td><?php echo htmlspecialchars($judicial['descripcion_judicial']); ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function mostrarClientesSinHistorial($clientes_sin_historial, $encabezados_sin_historial) {
    ?>
    <div class="client-box">
        <div class="client-header">
            <h4>Clientes sin Historial</h4>
        </div>
        <div class="client-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <?php if (in_array('Nombres', $encabezados_sin_historial)) : ?>
                            <th>Nombre Completo</th>
                        <?php endif; ?>
                        <?php if (in_array('DNI', $encabezados_sin_historial)) : ?>
                            <th>DNI</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes_sin_historial as $cliente) : ?>
                        <tr>
                            <?php if (in_array('Nombres', $encabezados_sin_historial)) : ?>
                                <td><?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']); ?></td>
                            <?php endif; ?>
                            <?php if (in_array('DNI', $encabezados_sin_historial)) : ?>
                                <td><?php echo htmlspecialchars($cliente['dni']); ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}
?>
