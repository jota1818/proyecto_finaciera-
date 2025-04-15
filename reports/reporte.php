<?php
require "../conexion_db/connection.php";

// Inicializar variables para el mes y año
$mes = isset($_POST['mes']) ? $_POST['mes'] : date('m');
$anio = isset($_POST['anio']) ? $_POST['anio'] : date('Y');

// Verificar si el formulario ha sido enviado
$reporte_generado = isset($_POST['mes']);

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
</head>

<body>
    <div class="container mt-5">
        <h2>Reporte de Historiales -
            <?php
            $meses = [
                '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
            ];
            echo $meses[$mes] . ' ' . $anio;
            ?>
        </h2>

        <!-- Formulario para seleccionar mes y año -->
        <form id="reporteForm" method="post" action="">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="mes">Mes:</label>
                    <select id="mes" name="mes" class="form-control" required>
                        <?php
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
            <br>
        </form>
        <!-- Botones de descarga y Salir -->
        <button type="button" class="btn btn-danger mt-3" onclick="window.location.href='../registro_cliente/index.php'">Salir</button>
        <?php if ($reporte_generado): ?>
            <button class="btn btn-primary" onclick="descargarReportePDF()">Descargar Reporte en PDF</button>
        <?php endif; ?>
        <br>

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
                            <h5>Etapa Pre-Judicial</h5>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Fecha Clave</th>
                                        <th>Acto</th>
                                        <th>Acción en Fecha Clave</th>
                                        <th>Descripción</th>
                                        <th>Objetivo Logrado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($prejudiciales as $prejudicial) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($prejudicial['fecha_acto']); ?></td>
                                            <td><?php echo htmlspecialchars($prejudicial['fecha_clave']); ?></td>
                                            <td><?php echo htmlspecialchars($prejudicial['acto']); ?></td>
                                            <td><?php echo htmlspecialchars($prejudicial['accion_fecha_clave']); ?></td>
                                            <td><?php echo htmlspecialchars($prejudicial['descripcion']); ?></td>
                                            <td><?php echo htmlspecialchars($prejudicial['objetivo_logrado']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>

                        <?php if (!empty($judiciales)) : ?>
                            <h5>Etapa Judicial</h5>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Fecha Clave</th>
                                        <th>Acto</th>
                                        <th>Acción en Fecha Clave</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($judiciales as $judicial) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($judicial['fecha_judicial']); ?></td>
                                            <td><?php echo htmlspecialchars($judicial['fecha_clave_judicial']); ?></td>
                                            <td><?php echo htmlspecialchars($judicial['acto_judicial']); ?></td>
                                            <td><?php echo htmlspecialchars($judicial['accion_en_fecha_clave']); ?></td>
                                            <td><?php echo htmlspecialchars($judicial['descripcion_judicial']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            <?php } ?>

            <!-- Clientes sin historial -->
            <?php if (!empty($clientes_sin_historial)) : ?>
                <div class="client-box">
                    <div class="client-header">
                        <h4>Clientes sin Historial</h4>
                    </div>
                    <div class="client-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre Completo</th>
                                    <th>DNI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientes_sin_historial as $cliente) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']); ?></td>
                                        <td><?php echo htmlspecialchars($cliente['dni']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        function descargarReportePDF() {
            // Crear un formulario oculto para enviar los datos del mes y año seleccionados
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'generar_pdf.php';
            form.style.display = 'none';

            // Agregar los campos del mes y año al formulario
            const mesInput = document.createElement('input');
            mesInput.type = 'hidden';
            mesInput.name = 'mes';
            mesInput.value = document.getElementById('mes').value;
            form.appendChild(mesInput);

            const anioInput = document.createElement('input');
            anioInput.type = 'hidden';
            anioInput.name = 'anio';
            anioInput.value = document.getElementById('anio').value;
            form.appendChild(anioInput);

            // Agregar el formulario al cuerpo del documento y enviarlo
            document.body.appendChild(form);
            form.submit();
        }

        function regresar() {
            window.location.href = '../registro_cliente/index.php';
        }
    </script>
</body>

</html>
