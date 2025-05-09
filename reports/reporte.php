<?php
require "../conexion_db/connection.php";
require "functions.php";

// Inicializar variables
$mes = isset($_POST['mes']) ? $_POST['mes'] : date('m');
$anio = isset($_POST['anio']) ? $_POST['anio'] : date('Y');
$filtro = isset($_POST['filtro']) ? $_POST['filtro'] : 'todos';
$filtroFecha = isset($_POST['filtroFecha']) ? $_POST['filtroFecha'] : 'mes_anio';

$encabezados_prejudicial = isset($_POST['encabezados_prejudicial']) ? $_POST['encabezados_prejudicial'] : [];
$encabezados_judicial = isset($_POST['encabezados_judicial']) ? $_POST['encabezados_judicial'] : [];
$encabezados_sin_historial = isset($_POST['encabezados_sin_historial']) ? $_POST['encabezados_sin_historial'] : [];

$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
$fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;

$reporte_generado = false;
$mensaje_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($filtroFecha === 'fecha_rango') {
        // Validar que ambas fechas estén llenas
        if (empty($fecha_inicio) || empty($fecha_fin)) {
            $mensaje_error = "⚠️ Debes seleccionar tanto la <strong>Fecha Inicio</strong> como la <strong>Fecha Fin</strong> para generar el reporte.";
        } else {
            list($clientes_prejudicial, $clientes_judicial, $clientes_sin_historial) = generarReporte($mes, $anio, $fecha_inicio, $fecha_fin, $conn);
            $reporte_generado = true;
        }
    } else {
        if (empty($anio)) {
            $mensaje_error = "⚠️ Debes seleccionar también el <strong>Año</strong> para filtrar por mes.";
        } else {
            list($clientes_prejudicial, $clientes_judicial, $clientes_sin_historial) = generarReporte($mes, $anio, null, null, $conn);
            $reporte_generado = true;
        }
    }
} else {
    // Carga inicial sin POST
    $reporte_generado = false;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Historiales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <div class="container-fluid mt-2">
        <?php include "templates.php"; ?>

        <div class="reporte-header mb-2">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="text-center w-100">REPORTE DE HISTORIALES -
                    <?php
                    if ($fecha_inicio && $fecha_fin) {
                        echo "Del " . formatDate($fecha_inicio) . " al " . formatDate($fecha_fin);
                    } else {
                        echo getMesAnio($mes, $anio);
                    }
                    ?>
                </h2>
            </div>

            <form id="reporteForm" method="post" action="" class="d-flex justify-content-center">
                <div class="me-2">
                    <label for="filtroFecha">Filtrar por:</label>
                    <select id="filtroFecha" name="filtroFecha" class="form-select form-select-sm" onchange="toggleDateFields()" required>
                        <option value="mes_anio" <?= $filtroFecha === 'mes_anio' ? 'selected' : '' ?>>Mes y Año</option>
                        <option value="fecha_rango" <?= $filtroFecha === 'fecha_rango' ? 'selected' : '' ?>>Fecha Inicio y Fin</option>
                    </select>
                </div>
                <div id="mesAnioFields" class="me-2" style="<?= $filtroFecha !== 'mes_anio' ? 'display: none;' : '' ?>">
                    <label for="mes">Mes:</label>
                    <select id="mes" name="mes" class="form-select form-select-sm">
                        <?php foreach (getMeses() as $value => $label): ?>
                            <option value="<?= $value; ?>" <?= ($value == $mes) ? 'selected' : '' ?>>
                                <?= $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="anio">Año:</label>
                    <input type="number" id="anio" name="anio" class="form-control form-control-sm" value="<?= $anio; ?>">
                </div>
                <div id="fechaRangoFields" class="me-2" style="<?= $filtroFecha !== 'fecha_rango' ? 'display: none;' : '' ?>">
                    <label for="fecha_inicio">Fecha Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control form-control-sm" value="<?= $fecha_inicio ?? ''; ?>">
                    <label for="fecha_fin">Fecha Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control form-control-sm" value="<?= $fecha_fin ?? ''; ?>">
                </div>
            </form>

            <!-- Aquí aparece SOLO el mensaje de error -->
            <?php if (!empty($mensaje_error)): ?>
                <div class="alert alert-warning text-center mt-3" role="alert">
                    <?= $mensaje_error; ?>
                </div>
            <?php endif; ?>

            <div class="btn-container mt-3">
                <button type="button" class="btn btn-secondary" onclick="abrirModal('con_historial')">Clientes con Historial</button>
                <button type="button" class="btn btn-secondary" onclick="abrirModal('sin_historial')">Clientes sin Historial</button>
                <button type="button" class="btn btn-secondary" onclick="abrirModal('todos')">Clientes con y sin Historial</button>
                <button type="button" class="btn btn-primary" onclick="abrirConfiguracion()">Configuración<i class="bi bi-gear-fill ms-3"></i></button>
                <button type="button" class="btn btn-danger btn-salir" onclick="window.location.href='../registro_cliente/index.php'">Salir</button>
            </div>
        </div>

        <?php if ($reporte_generado && !empty($mensaje_error) === false): ?>
            <?php mostrarClientes($filtro, $clientes_prejudicial, $clientes_judicial, $clientes_sin_historial, $encabezados_prejudicial, $encabezados_judicial, $encabezados_sin_historial); ?>
            <form id="descargarReporteForm" method="post" action="generar_pdf.php" target="_blank">
                <input type="hidden" name="mes" value="<?= $mes; ?>">
                <input type="hidden" name="anio" value="<?= $anio; ?>">
                <input type="hidden" name="filtro" value="<?= $filtro; ?>">
                <input type="hidden" name="fecha_inicio" value="<?= $fecha_inicio; ?>">
                <input type="hidden" name="fecha_fin" value="<?= $fecha_fin; ?>">
                <input type="hidden" name="encabezados_prejudicial" value="<?= implode(',', $encabezados_prejudicial); ?>">
                <input type="hidden" name="encabezados_judicial" value="<?= implode(',', $encabezados_judicial); ?>">
                <input type="hidden" name="encabezados_sin_historial" value="<?= implode(',', $encabezados_sin_historial); ?>">
                <button type="submit" class="btn btn-success mt-3">Descargar Reporte</button>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts.js"></script>
</body>
</html>