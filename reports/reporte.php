<?php
require "../conexion_db/connection.php";

// Inicializar variables para el mes y año
$mes = isset($_POST['mes']) ? $_POST['mes'] : date('m');
$anio = isset($_POST['anio']) ? $_POST['anio'] : date('Y');

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

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Reporte de Historiales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../styles.css" rel="stylesheet">
    <style>
        .client-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .client-header {
            background-color: #f9f9f9;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        .client-header h4 {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Reporte de Historiales - <?php echo date('F Y', strtotime($fecha_inicio)); ?></h2>

        <!-- Formulario para seleccionar mes y año -->
        <form id="reporteForm" method="post" action="">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="mes">Mes:</label>
                    <select id="mes" name="mes" class="form-control" required>
                        <?php
                        $meses = [
                            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
                        ];
                        foreach ($meses as $value => $label) {
                            echo "<option value='$value' " . ($value == $mes ? 'selected' : '') . ">$label</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="anio">Año:</label>
                    <input type="number" id="anio" name="anio" class="form-control" value="<?php echo $anio; ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Generar Reporte</button>
        </form>

        <!-- Historiales de Clientes -->
        <?php
        $clientes_prejudicial = [];
        while ($row = $result_prejudicial->fetch_assoc()) {
            $clientes_prejudicial[$row['id_cliente']][] = $row;
        }

        $clientes_judicial = [];
        while ($row = $result_judicial->fetch_assoc()) {
            $clientes_judicial[$row['id_cliente']][] = $row;
        }

        foreach ($clientes_prejudicial as $id_cliente => $prejudiciales) {
            $cliente = $prejudiciales[0];
            $judiciales = $clientes_judicial[$id_cliente] ?? [];
        ?>
            <div class="client-box">
                <div class="client-header">
                    <h4><?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']); ?></h4>
                </div>
                <div class="client-body">
                    <?php if (!empty($prejudiciales)) : ?>
                        <h5>Historial Pre-Judicial</h5>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha Acto</th>
                                    <th>Acto</th>
                                    <th>Descripción</th>
                                    <th>Fecha Clave</th>
                                    <th>Acción en Fecha Clave</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($prejudiciales as $prejudicial) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($prejudicial['fecha_acto']); ?></td>
                                        <td><?php echo htmlspecialchars($prejudicial['acto']); ?></td>
                                        <td><?php echo htmlspecialchars($prejudicial['descripcion']); ?></td>
                                        <td><?php echo htmlspecialchars($prejudicial['fecha_clave']); ?></td>
                                        <td><?php echo htmlspecialchars($prejudicial['accion_fecha_clave']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <?php if (!empty($judiciales)) : ?>
                        <h5>Historial Judicial</h5>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha Judicial</th>
                                    <th>Acto</th>
                                    <th>Descripción</th>
                                    <th>Fecha Clave</th>
                                    <th>Acción en Fecha Clave</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($judiciales as $judicial) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($judicial['fecha_judicial']); ?></td>
                                        <td><?php echo htmlspecialchars($judicial['acto_judicial']); ?></td>
                                        <td><?php echo htmlspecialchars($judicial['descripcion_judicial']); ?></td>
                                        <td><?php echo htmlspecialchars($judicial['fecha_clave_judicial']); ?></td>
                                        <td><?php echo htmlspecialchars($judicial['accion_en_fecha_clave']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        <?php } ?>

        <!-- Botón de descarga -->
        <button class="btn btn-primary" onclick="descargarReporte()">Descargar Reporte</button>
    </div>

    <script>
        function descargarReporte() {
            // Convertir la tabla a un archivo CSV
            const tables = document.querySelectorAll('table');
            let csvContent = '';

            tables.forEach((table, index) => {
                const rows = table.querySelectorAll('tr');
                rows.forEach(row => {
                    const cells = row.querySelectorAll('th, td');
                    const rowData = Array.from(cells).map(cell => cell.innerText).join(',');
                    csvContent += rowData + '\n';
                });

                // Agregar una línea en blanco entre tablas
                if (index < tables.length - 1) {
                    csvContent += '\n';
                }
            });

            // Crear un enlace para descargar el archivo
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'reporte_' + <?php echo json_encode($mes . '_' . $anio); ?> + '.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    </script>
</body>

</html>
