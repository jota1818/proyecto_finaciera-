<?php
function formatDate($date)
{
    return date('d-m-Y', strtotime($date));
}

function getMesAnio($mes, $anio)
{
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
    return $meses[$mes] . ' ' . $anio;
}

function getMeses()
{
    return [
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
}

function generarReporte($mes, $anio, $conn)
{
    $fecha_inicio = $anio . '-' . $mes . '-01';
    $fecha_fin = date('Y-m-t', strtotime($fecha_inicio));

    $sql_prejudicial = "SELECT c.*, p.* FROM etapa_prejudicial p JOIN clientes c ON p.id_cliente = c.id_cliente WHERE p.fecha_acto BETWEEN '$fecha_inicio' AND '$fecha_fin' ORDER BY c.nombre, c.apellidos, p.fecha_acto";
    $result_prejudicial = $conn->query($sql_prejudicial);

    $sql_judicial = "SELECT c.*, j.* FROM etapa_judicial j JOIN clientes c ON j.id_cliente = c.id_cliente WHERE j.fecha_judicial BETWEEN '$fecha_inicio' AND '$fecha_fin' ORDER BY c.nombre, c.apellidos, j.fecha_judicial";
    $result_judicial = $conn->query($sql_judicial);

    $sql_clientes = "SELECT * FROM clientes ORDER BY nombre, apellidos";
    $result_clientes = $conn->query($sql_clientes);

    $clientes_con_historial = [];
    while ($row = $result_prejudicial->fetch_assoc()) {
        $clientes_con_historial[$row['id_cliente']] = true;
    }
    while ($row = $result_judicial->fetch_assoc()) {
        $clientes_con_historial[$row['id_cliente']] = true;
    }

    $clientes_sin_historial = [];
    while ($row = $result_clientes->fetch_assoc()) {
        if (!isset($clientes_con_historial[$row['id_cliente']])) {
            $clientes_sin_historial[] = $row;
        }
    }

    $clientes_prejudicial = [];
    $result_prejudicial->data_seek(0);
    while ($row = $result_prejudicial->fetch_assoc()) {
        $clientes_prejudicial[$row['id_cliente']][] = $row;
    }

    $clientes_judicial = [];
    $result_judicial->data_seek(0);
    while ($row = $result_judicial->fetch_assoc()) {
        $clientes_judicial[$row['id_cliente']][] = $row;
    }

    return [$clientes_prejudicial, $clientes_judicial, $clientes_sin_historial];
}

function mostrarClientes($filtro, $clientes_prejudicial, $clientes_judicial, $clientes_sin_historial, $encabezados_prejudicial, $encabezados_judicial, $encabezados_sin_historial)
{
    if ($filtro === 'con_historial') {
        foreach ($clientes_prejudicial as $id_cliente => $prejudiciales) {
            $cliente = $prejudiciales[0];
            $judiciales = $clientes_judicial[$id_cliente] ?? [];
            mostrarCliente($cliente, $prejudiciales, $judiciales, $encabezados_prejudicial, $encabezados_judicial);
        }
    } elseif ($filtro === 'sin_historial') {
        mostrarClientesSinHistorial($clientes_sin_historial, $encabezados_sin_historial);
    } else {
        foreach ($clientes_prejudicial as $id_cliente => $prejudiciales) {
            $cliente = $prejudiciales[0];
            $judiciales = $clientes_judicial[$id_cliente] ?? [];
            mostrarCliente($cliente, $prejudiciales, $judiciales, $encabezados_prejudicial, $encabezados_judicial);
        }
        mostrarClientesSinHistorial($clientes_sin_historial, $encabezados_sin_historial);
    }
}

function mostrarCliente($cliente, $prejudiciales, $judiciales, $encabezados_prejudicial, $encabezados_judicial)
{
?>
    <div class="client-box">
        <div class="client-header">
            <h4><?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']); ?></h4>
        </div>
        <div class="client-body">
            <?php if (!empty($prejudiciales) && mostrarEncabezados($encabezados_prejudicial)): ?>
                <h5>Etapa Pre-Judicial</h5>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <?php foreach ($encabezados_prejudicial as $encabezado): ?>
                                <th><?php echo $encabezado; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prejudiciales as $prejudicial): ?>
                            <tr>
                                <?php if (in_array('Fecha', $encabezados_prejudicial)): ?>
                                    <td><?php echo formatDate($prejudicial['fecha_acto']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Fecha Clave', $encabezados_prejudicial)): ?>
                                    <td><?php echo formatDate($prejudicial['fecha_clave']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Acto', $encabezados_prejudicial)): ?>
                                    <td><?php echo htmlspecialchars($prejudicial['acto']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Acción en Fecha Clave', $encabezados_prejudicial)): ?>
                                    <td><?php echo htmlspecialchars($prejudicial['accion_fecha_clave']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Descripción', $encabezados_prejudicial)): ?>
                                    <td><?php echo htmlspecialchars($prejudicial['descripcion']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Objetivo Logrado', $encabezados_prejudicial)): ?>
                                    <td><?php echo htmlspecialchars($prejudicial['objetivo_logrado']); ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if (!empty($judiciales) && mostrarEncabezados($encabezados_judicial)): ?>
                <h5>Etapa Judicial</h5>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <?php foreach ($encabezados_judicial as $encabezado): ?>
                                <th><?php echo $encabezado; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($judiciales as $judicial): ?>
                            <tr>
                                <?php if (in_array('Fecha', $encabezados_judicial)): ?>
                                    <td><?php echo formatDate($judicial['fecha_judicial']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Fecha Clave', $encabezados_judicial)): ?>
                                    <td><?php echo formatDate($judicial['fecha_clave_judicial']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Acto', $encabezados_judicial)): ?>
                                    <td><?php echo htmlspecialchars($judicial['acto_judicial']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Acción en Fecha Clave', $encabezados_judicial)): ?>
                                    <td><?php echo htmlspecialchars($judicial['accion_en_fecha_clave']); ?></td>
                                <?php endif; ?>
                                <?php if (in_array('Descripción', $encabezados_judicial)): ?>
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

function mostrarClientesSinHistorial($clientes_sin_historial, $encabezados_sin_historial)
{
?>
    <div class="client-box">
        <div class="client-header">
            <h4>Clientes sin Historial</h4>
        </div>
        <div class="client-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <?php foreach ($encabezados_sin_historial as $encabezado): ?>
                            <th><?php echo $encabezado; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes_sin_historial as $cliente): ?>
                        <tr>
                            <?php if (in_array('Nombres', $encabezados_sin_historial)): ?>
                                <td><?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']); ?></td>
                            <?php endif; ?>
                            <?php if (in_array('DNI', $encabezados_sin_historial)): ?>
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

function mostrarEncabezados($encabezados)
{
    return !empty(array_filter($encabezados, function ($encabezado) {
        return in_array($encabezado, ['Fecha', 'Fecha Clave', 'Acto', 'Acción en Fecha Clave', 'Descripción', 'Objetivo Logrado']);
    }));
}
?>