<?php
require "../conexion_db/connection.php";

$id_cliente = isset($_GET['id_cliente']) ? $_GET['id_cliente'] : '';
// solo es para iniciacilar los valores, pero se puede quitar este codigo con normalidad
$cliente = [];
$monto_abonado = 0; // Inicializar con un valor predeterminado
$plazo_credito = 0; // Inicializar con un valor predeterminado

if ($id_cliente) {
    // Obtener información del cliente
    $sql_cliente = "SELECT nombre, apellidos, dni, agencia, tipo_credito, monto, saldo, fecha_desembolso, fecha_vencimiento FROM clientes WHERE id_cliente = '$id_cliente'";
    $result_cliente = $conn->query($sql_cliente);

    if ($result_cliente->num_rows > 0) {
        $cliente = $result_cliente->fetch_assoc();

        // Calcular el plazo de crédito
        $fecha_desembolso = new DateTime($cliente['fecha_desembolso']);
        $fecha_vencimiento = new DateTime($cliente['fecha_vencimiento']);
        $plazo_credito = $fecha_vencimiento->diff($fecha_desembolso)->days;
        // Calcular el monto abonado
        $monto_abonado = $cliente['monto'] - $cliente['saldo'];
    } else {
        die("Error: El ID del cliente no existe en la base de datos.");
    }

    // Obtener historial pre-judicial
    $sql_prejudicial = "SELECT * FROM etapa_prejudicial WHERE id_cliente = '$id_cliente'";
    $result_prejudicial = $conn->query($sql_prejudicial);

    // Obtener historial judicial
    $sql_judicial = "SELECT * FROM etapa_judicial WHERE id_cliente = '$id_cliente'";
    $result_judicial = $conn->query($sql_judicial);
} else {
    die("Error: ID del cliente no proporcionado.");
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Historial del Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>

<body class="container mt-3">
    <h2>Historial del Cliente</h2>
    <div class="client-info border p-3 mb-3">
        <h4>Información del Cliente</h4>
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']); ?></p>
        <p><strong>DNI:</strong> <?php echo htmlspecialchars($cliente['dni']); ?></p>
        <p><strong>Agencia:</strong> <?php echo htmlspecialchars($cliente['agencia']); ?></p>
        <p><strong>Tipo de Crédito:</strong> <?php echo htmlspecialchars($cliente['tipo_credito']); ?></p>
        <p><strong>Monto:</strong> <?php echo htmlspecialchars($cliente['monto']); ?></p>
        <p><strong>Saldo:</strong> <?php echo htmlspecialchars($cliente['saldo']); ?></p>
        <p><strong>Monto Abonado:</strong> <?php echo htmlspecialchars($monto_abonado); ?></p>
        <p><strong>Fecha de Desembolso:</strong> <?php echo htmlspecialchars($cliente['fecha_desembolso']); ?></p>
        <p><strong>Fecha de Vencimiento:</strong> <?php echo htmlspecialchars($cliente['fecha_vencimiento']); ?></p>
        <p><strong>Plazo de Crédito (días):</strong> <?php echo htmlspecialchars($plazo_credito); ?></p>
    </div>

    <div class="historial border p-3">
        <h4>Historial Pre-Judicial</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Fecha Acto</th>
                        <th>Acto</th>
                        <th>N° de Notif. Voucher</th>
                        <th>Descripción</th>
                        <th>Notificación</th>
                        <th>Fecha Clave</th>
                        <th>Acción en Fecha Clave</th>
                        <th>Actor</th>
                        <th>Evidencia 1</th>
                        <th>Evidencia 2</th>
                        <th>Días desde Fecha Clave</th>
                        <th>Objetivo Logrado</th>
                        <th>Días de Mora</th>
                        <th>Días Mora PJ</th>
                        <th>Interés</th>
                        <th>Saldo más Interés</th>
                        <th>Monto Amortizado</th>
                        <th>Saldo a la Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 1;
                    while ($row = $result_prejudicial->fetch_assoc()) {
                        echo "<tr>
                                <td>{$count}</td>
                                <td>{$row['fecha_acto']}</td>
                                <td>{$row['acto']}</td>
                                <td>{$row['n_de_notif_voucher']}</td>
                                <td>{$row['descripcion']}</td>
                                <td>" . (isset($row['notif_compromiso_pago_evidencia']) && !empty($row['notif_compromiso_pago_evidencia']) && $row['notif_compromiso_pago_evidencia'] !== 'uploads/' ? "<a href='#' class='file-link' data-url='../pre_judicial/{$row['notif_compromiso_pago_evidencia']}'>{$row['notif_compromiso_pago_evidencia']}</a> <a href='../pre_judicial/{$row['notif_compromiso_pago_evidencia']}' download style='margin-left:10px; color:blue; font-weight:bold;'>📥Descargar</a>" : '') . "</td>
                                <td>{$row['fecha_clave']}</td>
                                <td>{$row['accion_fecha_clave']}</td>
                                <td>{$row['actor']}</td>
                                <td>" . (isset($row['evidencia1_localizacion']) && !empty($row['evidencia1_localizacion']) && $row['evidencia1_localizacion'] !== 'uploads/' ? "<a href='#' class='file-link' data-url='../pre_judicial/{$row['evidencia1_localizacion']}'>{$row['evidencia1_localizacion']}</a> <a href='../pre_judicial/{$row['evidencia1_localizacion']}' download style='margin-left:10px; color:blue; font-weight:bold;' >📥Descargar</a>" : '') . "</td>
                                <td>" . (isset($row['evidencia2_foto_fecha']) && !empty($row['evidencia2_foto_fecha']) && $row['evidencia2_foto_fecha'] !== 'uploads/' ? "<a href='#' class='file-link' data-url='../pre_judicial/{$row['evidencia2_foto_fecha']}'>{$row['evidencia2_foto_fecha']}</a> <a href='../pre_judicial/{$row['evidencia2_foto_fecha']}' download style='margin-left:10px; color:blue; font-weight:bold;'>📥Descargar</a>" : '') . "</td>
                                <td>{$row['dias_desde_fecha_clave']}</td>
                                <td>{$row['objetivo_logrado']}</td>
                                <td>{$row['dias_de_mora']}</td>
                                <td>{$row['dias_mora_PJ']}</td>
                                <td>{$row['interes']}</td>
                                <td>{$row['saldo_int']}</td>
                                <td>{$row['monto_amortizado']}</td>
                                <td>{$row['saldo_fecha']}</td>
                              </tr>";
                        $count++;
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <h4>Historial Judicial</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Etapa</th>
                        <th>Fecha</th>
                        <th>Acto</th>
                        <th>Juzgado</th>
                        <th>Num. Expediente</th>
                        <th>Num. Cédula</th>
                        <th>Descripción</th>
                        <th>Doc. Evidencia</th>
                        <th>Fecha Clave</th>
                        <th>Acción en Fecha Clave</th>
                        <th>Actor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result_judicial->fetch_assoc()) {
                        echo "<tr>
                                <td>{$count}</td>
                                <td>{$row['etapa']}</td>
                                <td>{$row['fecha_judicial']}</td>
                                <td>{$row['acto_judicial']}</td>
                                <td>{$row['juzgado']}</td>
                                <td>{$row['n_exp_juzgado']}</td>
                                <td>{$row['n_cedula']}</td>
                                <td>{$row['descripcion_judicial']}</td>
                                <td>" . (isset($row['doc_evidencia']) && !empty($row['doc_evidencia']) && $row['doc_evidencia'] !== 'uploads/' ? "<a href='#' class='file-link' data-url='../judicial/{$row['doc_evidencia']}'>{$row['doc_evidencia']}</a> <a href='../judicial/{$row['doc_evidencia']}' download style='margin-left:10px; color:blue; font-weight:bold;'>📥Descargar</a>" : '') . "</td>
                                <td>{$row['fecha_clave_judicial']}</td>
                                <td>{$row['accion_en_fecha_clave']}</td>
                                <td>{$row['actor_judicial']}</td>
                              </tr>";
                        $count++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="fixed-buttons mt-3">
        <button type="button" class="btn btn-primary" onclick="agregarHistoria()">Agregar Historia</button>
        <button type="button" class="btn btn-secondary" onclick="history.back()">Regresar</button>
        <button type="button" class="btn btn-danger" onclick="window.location.href='../registro_cliente/index.php'">Salir</button>
    </div>

    <!-- Contenedor de vista previa -->
    <div class="preview-container" id="previewContainer">
        <button class="btn btn-danger" onclick="closePreview()">Cerrar</button>
        <iframe id="previewFrame"></iframe>
    </div>

    <script>
        function agregarHistoria() {
            var idCliente = <?php echo json_encode($id_cliente); ?>;
            if (idCliente) {
                window.location.href = '/proyecto_financiera/pre_judicial/registro_prejudicial.php?id_cliente=' + encodeURIComponent(idCliente);
            } else {
                alert("Error: ID del cliente no disponible.");
            }
        }

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('file-link')) {
                event.preventDefault();
                var fileUrl = event.target.getAttribute('data-url');
                openPreview(fileUrl);
            }
        });

        //Para la vista previa
        function openPreview(url) {
            var previewContainer = document.getElementById('previewContainer');
            var previewFrame = document.getElementById('previewFrame');
            previewFrame.src = url;
            previewFrame.onload = function() {
                previewContainer.style.display = 'flex'; // Usa flex para centrar
            };
            previewFrame.onerror = function() {
                alert('El archivo no se puede mostrar. Verifica la URL o la existencia del archivo.');
            };
        }

        function closePreview() {
            var previewContainer = document.getElementById('previewContainer');
            previewContainer.style.display = 'none';
        }
    </script>
</body>

</html>
