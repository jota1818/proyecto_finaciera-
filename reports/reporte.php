<?php
require "../conexion_db/connection.php";

require "functions.php";

// Inicializar variables para el mes y año
$mes = isset($_POST['mes']) ? $_POST['mes'] : date('m');
$anio = isset($_POST['anio']) ? $_POST['anio'] : date('Y');
$filtro = isset($_POST['filtro']) ? $_POST['filtro'] : 'todos';
$encabezados_prejudicial = isset($_POST['encabezados_prejudicial']) ? $_POST['encabezados_prejudicial'] : [];
$encabezados_judicial = isset($_POST['encabezados_judicial']) ? $_POST['encabezados_judicial'] : [];
$encabezados_sin_historial = isset($_POST['encabezados_sin_historial']) ? $_POST['encabezados_sin_historial'] : [];

$reporte_generado = isset($_POST['filtro']);

if ($reporte_generado) {
    list($clientes_prejudicial, $clientes_judicial, $clientes_sin_historial) = generarReporte($mes, $anio, $conn);
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
                <h2 class="text-center w-100">REPORTE DE HISTORIALES - <?php echo getMesAnio($mes, $anio); ?></h2>

            </div>

            <form id="reporteForm" method="post" action="" class="d-flex justify-content-center">
                <div class="me-2">
                    <label for="mes">Mes:</label>
                    <select id="mes" name="mes" class="form-select form-select-sm" required>
                        <?php foreach (getMeses() as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php echo ($value == $mes) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="me-2">
                    <label for="anio">Año:</label>
                    <input type="number" id="anio" name="anio" class="form-control form-control-sm" value="<?php echo $anio; ?>" required>
                </div>
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