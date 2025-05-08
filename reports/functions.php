<?php
function formatDate($date) {
    return date('d-m-Y', strtotime($date));
}

function getMesAnio($mes, $anio) {
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

function getMeses() {
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

function generarReporte($mes, $anio, $fecha_inicio, $fecha_fin, $conn) {
    $clientes_prejudicial = [];
    $clientes_judicial = [];
    $clientes_con_historial = [];
    $clientes_sin_historial = [];

    if ($fecha_inicio && $fecha_fin) {
        // Usar rango de fechas
        $sql_prejudicial = "SELECT c.*, p.* FROM etapa_prejudicial p JOIN clientes c ON p.id_cliente = c.id_cliente WHERE p.fecha_acto BETWEEN ? AND ? ORDER BY c.nombre, c.apellidos, p.fecha_acto";
        $sql_judicial = "SELECT c.*, j.* FROM etapa_judicial j JOIN clientes c ON j.id_cliente = c.id_cliente WHERE j.fecha_judicial BETWEEN ? AND ? ORDER BY c.nombre, c.apellidos, j.fecha_judicial";

        $stmt_prejudicial = $conn->prepare($sql_prejudicial);
        $stmt_prejudicial->bind_param("ss", $fecha_inicio, $fecha_fin);
        $stmt_prejudicial->execute();
        $result_prejudicial = $stmt_prejudicial->get_result();

        while ($row = $result_prejudicial->fetch_assoc()) {
            $clientes_prejudicial[$row['id_cliente']][] = $row;
            $clientes_con_historial[$row['id_cliente']] = true;
        }

        $stmt_judicial = $conn->prepare($sql_judicial);
        $stmt_judicial->bind_param("ss", $fecha_inicio, $fecha_fin);
        $stmt_judicial->execute();
        $result_judicial = $stmt_judicial->get_result();

        while ($row = $result_judicial->fetch_assoc()) {
            $clientes_judicial[$row['id_cliente']][] = $row;
            $clientes_con_historial[$row['id_cliente']] = true;
        }
    } else {
        // Usar mes y año
        $fecha_inicio = $anio . '-' . $mes . '-01';
        $fecha_fin = date('Y-m-t', strtotime($fecha_inicio));
        $sql_prejudicial = "SELECT c.*, p.* FROM etapa_prejudicial p JOIN clientes c ON p.id_cliente = c.id_cliente WHERE p.fecha_acto BETWEEN ? AND ? ORDER BY c.nombre, c.apellidos, p.fecha_acto";
        $sql_judicial = "SELECT c.*, j.* FROM etapa_judicial j JOIN clientes c ON j.id_cliente = c.id_cliente WHERE j.fecha_judicial BETWEEN ? AND ? ORDER BY c.nombre, c.apellidos, j.fecha_judicial";

        $stmt_prejudicial = $conn->prepare($sql_prejudicial);
        $stmt_prejudicial->bind_param("ss", $fecha_inicio, $fecha_fin);
        $stmt_prejudicial->execute();
        $result_prejudicial = $stmt_prejudicial->get_result();

        while ($row = $result_prejudicial->fetch_assoc()) {
            $clientes_prejudicial[$row['id_cliente']][] = $row;
            $clientes_con_historial[$row['id_cliente']] = true;
        }

        $stmt_judicial = $conn->prepare($sql_judicial);
        $stmt_judicial->bind_param("ss", $fecha_inicio, $fecha_fin);
        $stmt_judicial->execute();
        $result_judicial = $stmt_judicial->get_result();

        while ($row = $result_judicial->fetch_assoc()) {
            $clientes_judicial[$row['id_cliente']][] = $row;
            $clientes_con_historial[$row['id_cliente']] = true;
        }
    }

    // Fetch all clients
    $result_clientes = $conn->query("SELECT * FROM clientes ORDER BY nombre, apellidos");
    while ($row = $result_clientes->fetch_assoc()) {
        if (!array_key_exists($row['id_cliente'], $clientes_con_historial)) {
            $clientes_sin_historial[] = $row;
        }
    }

    return [$clientes_prejudicial, $clientes_judicial, $clientes_sin_historial];
}

function mostrarClientes($filtro, $clientes_prejudicial, $clientes_judicial, $clientes_sin_historial, $encabezados_prejudicial, $encabezados_judicial, $encabezados_sin_historial) {
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

function mostrarCliente($cliente, $prejudiciales, $judiciales, $encabezados_prejudicial, $encabezados_judicial) {
    ?>
    <div class="client-box">
        <div class="client-header">
            <h4><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']) ?></h4>
        </div>
        <div class="client-body">
            <?php if (!empty($prejudiciales) && mostrarEncabezados($encabezados_prejudicial)): ?>
                <h5>Etapa Pre-Judicial</h5>
                <table class="table table-bordered table-striped">
                    <thead><tr>
                        <?php foreach ($encabezados_prejudicial as $encabezado): ?>
                            <th><?= $encabezado ?></th>
                        <?php endforeach; ?>
                    </tr></thead>
                    <tbody>
                        <?php foreach ($prejudiciales as $p): ?>
                            <tr>
                                <?php if (in_array('Fecha', $encabezados_prejudicial)): ?><td><?= formatDate($p['fecha_acto']) ?></td><?php endif; ?>
                                <?php if (in_array('Fecha Clave', $encabezados_prejudicial)): ?><td><?= formatDate($p['fecha_clave']) ?></td><?php endif; ?>
                                <?php if (in_array('Acto', $encabezados_prejudicial)): ?><td><?= htmlspecialchars($p['acto']) ?></td><?php endif; ?>
                                <?php if (in_array('Acción en Fecha Clave', $encabezados_prejudicial)): ?><td><?= htmlspecialchars($p['accion_fecha_clave']) ?></td><?php endif; ?>
                                <?php if (in_array('Descripción', $encabezados_prejudicial)): ?><td><?= htmlspecialchars($p['descripcion']) ?></td><?php endif; ?>
                                <?php if (in_array('Objetivo Logrado', $encabezados_prejudicial)): ?><td><?= htmlspecialchars($p['objetivo_logrado']) ?></td><?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <?php if (!empty($judiciales) && mostrarEncabezados($encabezados_judicial)): ?>
                <h5>Etapa Judicial</h5>
                <table class="table table-bordered table-striped">
                    <thead><tr>
                        <?php foreach ($encabezados_judicial as $encabezado): ?>
                            <th><?= $encabezado ?></th>
                        <?php endforeach; ?>
                    </tr></thead>
                    <tbody>
                        <?php foreach ($judiciales as $j): ?>
                            <tr>
                                <?php if (in_array('Fecha', $encabezados_judicial)): ?><td><?= formatDate($j['fecha_judicial']) ?></td><?php endif; ?>
                                <?php if (in_array('Fecha Clave', $encabezados_judicial)): ?><td><?= formatDate($j['fecha_clave_judicial']) ?></td><?php endif; ?>
                                <?php if (in_array('Acto', $encabezados_judicial)): ?><td><?= htmlspecialchars($j['acto_judicial']) ?></td><?php endif; ?>
                                <?php if (in_array('Acción en Fecha Clave', $encabezados_judicial)): ?><td><?= htmlspecialchars($j['accion_en_fecha_clave']) ?></td><?php endif; ?>
                                <?php if (in_array('Descripción', $encabezados_judicial)): ?><td><?= htmlspecialchars($j['descripcion_judicial']) ?></td><?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function mostrarClientesSinHistorial($clientes, $encabezados) {
    ?>
    <div class="client-box">
        <div class="client-header">
            <h4>Clientes sin Historial</h4>
        </div>
        <div class="client-body">
            <table class="table table-bordered table-striped">
                <thead><tr>
                    <?php foreach ($encabezados as $encabezado): ?>
                        <th><?= $encabezado ?></th>
                    <?php endforeach; ?>
                </tr></thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <?php if (in_array('Nombres', $encabezados)): ?><td><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']) ?></td><?php endif; ?>
                            <?php if (in_array('DNI', $encabezados)): ?><td><?= htmlspecialchars($cliente['dni']) ?></td><?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

function mostrarEncabezados($encabezados) {
    return !empty(array_filter($encabezados, function ($encabezado) {
        return in_array($encabezado, ['Fecha', 'Fecha Clave', 'Acto', 'Acción en Fecha Clave', 'Descripción', 'Objetivo Logrado']);
    }));
}
?>