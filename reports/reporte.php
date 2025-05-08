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

$reporte_generado = isset($_POST['filtro']);

if ($reporte_generado) {
    list($clientes_prejudicial, $clientes_judicial, $clientes_sin_historial) = generarReporte($mes, $anio, $fecha_inicio, $fecha_fin, $conn);
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
                        <option value="mes_anio" <?php echo ($_POST['filtroFecha'] ?? 'mes_anio') === 'mes_anio' ? 'selected' : ''; ?>>Mes y Año</option>
                        <option value="fecha_rango" <?php echo ($_POST['filtroFecha'] ?? 'mes_anio') === 'fecha_rango' ? 'selected' : ''; ?>>Fecha Inicio y Fin</option>
                    </select>
                </div>
                <div id="mesAnioFields" class="me-2" style="<?php echo ($_POST['filtroFecha'] ?? 'mes_anio') !== 'mes_anio' ? 'display: none;' : ''; ?>">
                    <label for="mes">Mes:</label>
                    <select id="mes" name="mes" class="form-select form-select-sm">
                        <?php foreach (getMeses() as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php echo ($value == $mes) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="anio">Año:</label>
                    <input type="number" id="anio" name="anio" class="form-control form-control-sm" value="<?php echo $anio; ?>">
                </div>
                <div id="fechaRangoFields" class="me-2" style="<?php echo ($_POST['filtroFecha'] ?? 'mes_anio') !== 'fecha_rango' ? 'display: none;' : ''; ?>">
                    <label for="fecha_inicio">Fecha Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control form-control-sm" value="<?php echo $_POST['fecha_inicio'] ?? ''; ?>">
                    <label for="fecha_fin">Fecha Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control form-control-sm" value="<?php echo $_POST['fecha_fin'] ?? ''; ?>">
                </div>
                <input type="hidden" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>">
                <input type="hidden" name="fecha_fin" value="<?php echo $fecha_fin; ?>">
            </form>

            <div class="btn-container">
                <button type="button" class="btn btn-secondary" onclick="abrirModal('con_historial')">Clientes con Historial</button>
                <button type="button" class="btn btn-secondary" onclick="abrirModal('sin_historial')">Clientes sin Historial</button>
                <button type="button" class="btn btn-secondary" onclick="abrirModal('todos')">Clientes con y sin Historial</button>
                <button type="button" class="btn btn-primary" onclick="abrirConfiguracion()">Configuración<i class="bi bi-gear-fill ms-3"></i></button>
                <button type="button" class="btn btn-danger btn-salir" onclick="window.location.href='../registro_cliente/index.php'">Salir</button>
            </div>
        </div>

        <?php if ($reporte_generado): ?>
            <?php mostrarClientes($filtro, $clientes_prejudicial, $clientes_judicial, $clientes_sin_historial, $encabezados_prejudicial, $encabezados_judicial, $encabezados_sin_historial); ?>
            <form id="descargarReporteForm" method="post" action="generar_pdf.php" target="_blank">
                <input type="hidden" name="mes" value="<?php echo $mes; ?>">
                <input type="hidden" name="anio" value="<?php echo $anio; ?>">
                <input type="hidden" name="filtro" value="<?php echo $filtro; ?>">
                <input type="hidden" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>">
                <input type="hidden" name="fecha_fin" value="<?php echo $fecha_fin; ?>">
                <input type="hidden" name="encabezados_prejudicial" value="<?php echo implode(',', $encabezados_prejudicial); ?>">
                <input type="hidden" name="encabezados_judicial" value="<?php echo implode(',', $encabezados_judicial); ?>">
                <input type="hidden" name="encabezados_sin_historial" value="<?php echo implode(',', $encabezados_sin_historial); ?>">
                <button type="submit" class="btn btn-success mt-3">Descargar Reporte</button>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts.js"></script>
</body>

</html>