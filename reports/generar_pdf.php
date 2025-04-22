<?php
require_once '../tcpdf/tcpdf.php';
require "../conexion_db/connection.php";

// Obtener parámetros del formulario
$mes = isset($_POST['mes']) ? $_POST['mes'] : date('m');
$anio = isset($_POST['anio']) ? $_POST['anio'] : date('Y');
$encabezados_prejudicial = isset($_POST['encabezados_prejudicial']) ? explode(',', $_POST['encabezados_prejudicial']) : [];
$encabezados_judicial = isset($_POST['encabezados_judicial']) ? explode(',', $_POST['encabezados_judicial']) : [];
$encabezados_sin_historial = isset($_POST['encabezados_sin_historial']) ? explode(',', $_POST['encabezados_sin_historial']) : [];

// Convertir el mes y año a un rango de fechas
$fecha_inicio = $anio . '-' . $mes . '-01';
$fecha_fin = date('Y-m-t', strtotime($fecha_inicio));

// Consultas para obtener los datos necesarios
$sql_prejudicial = "
    SELECT c.*, p.*
    FROM etapa_prejudicial p
    JOIN clientes c ON p.id_cliente = c.id_cliente
    WHERE p.fecha_acto BETWEEN '$fecha_inicio' AND '$fecha_fin'
    ORDER BY c.nombre, c.apellidos, p.fecha_acto
";
$result_prejudicial = $conn->query($sql_prejudicial);

$sql_judicial = "
    SELECT c.*, j.*
    FROM etapa_judicial j
    JOIN clientes c ON j.id_cliente = c.id_cliente
    WHERE j.fecha_judicial BETWEEN '$fecha_inicio' AND '$fecha_fin'
    ORDER BY c.nombre, c.apellidos, j.fecha_judicial
";
$result_judicial = $conn->query($sql_judicial);

$sql_clientes = "SELECT * FROM clientes ORDER BY nombre, apellidos";
$result_clientes = $conn->query($sql_clientes);

$conn->close();

// Crear una instancia de TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Reporte de Historiales');
$pdf->SetSubject('Reporte de Historiales');
$pdf->SetKeywords('Reporte, Historiales, PDF');

// Establecer el formato del papel y las márgenes
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 10);
$pdf->AddPage();
$pdf->SetCellPadding(2);

// Establecer la fuente
$pdf->SetFont('helvetica', '', 10);

// Agregar el título del reporte
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
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Reporte de Historiales - ' . $meses[$mes] . ' ' . $anio, 0, 1, 'C');
$pdf->Ln(5);

// Agregar los datos de los clientes
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

foreach ($clientes_prejudicial as $id_cliente => $prejudiciales) {
    $cliente = $prejudiciales[0];
    $judiciales = $clientes_judicial[$id_cliente] ?? [];

    // Agregar el nombre del cliente
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Cliente: ' . $cliente['nombre'] . ' ' . $cliente['apellidos'], 0, 1, 'L');
    $pdf->Ln(-2);

    // Agregar el historial pre-judicial
    if (!empty($prejudiciales) && !empty($encabezados_prejudicial)) {
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(0, 10, 'Etapa Pre-Judicial', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 8);

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        $pdf->SetFont('', 'B');

        // Encabezados de la tabla
        $header = [];
        $w = [];
        if (in_array('Fecha', $encabezados_prejudicial)) {
            $header[] = 'Fecha';
            $w[] = 20;
        }
        if (in_array('Fecha Clave', $encabezados_prejudicial)) {
            $header[] = 'Fecha Clave';
            $w[] = 20;
        }
        if (in_array('Acto', $encabezados_prejudicial)) {
            $header[] = 'Acto';
            $w[] = 25;
        }
        if (in_array('Acción en Fecha Clave', $encabezados_prejudicial)) {
            $header[] = 'Acción en Fecha Clave';
            $w[] = 40;
        }
        if (in_array('Descripción', $encabezados_prejudicial)) {
            $header[] = 'Descripción';
            $w[] = 50;
        }
        if (in_array('Objetivo Logrado', $encabezados_prejudicial)) {
            $header[] = 'Objetivo Logrado';
            $w[] = 30;
        }

        foreach ($header as $i => $col) {
            $pdf->Cell($w[$i], 7, $col, 1, 0, 'C', 1);
        }
        $pdf->Ln();

        $pdf->SetFont('', '');
        $fill = 0;
        foreach ($prejudiciales as $prejudicial) {
            foreach ($header as $i => $col) {
                $pdf->Cell($w[$i], 7, $prejudicial[$i == 0 ? 'fecha_acto' : ($i == 1 ? 'fecha_clave' : ($i == 2 ? 'acto' : ($i == 3 ? 'accion_fecha_clave' : ($i == 4 ? 'descripcion' : 'objetivo_logrado'))))], 1, 0, 'L', $fill);
            }
            $pdf->Ln();
            $fill = !$fill;
        }
        $pdf->Cell(array_sum($w), 0, '', 'T');
        $pdf->Ln();
    }

    // Agregar el historial judicial
    if (!empty($judiciales) && !empty($encabezados_judicial)) {
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(0, 10, 'Etapa Judicial', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 8);

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        $pdf->SetFont('', 'B');

        // Encabezados de la tabla
        $header = [];
        $w = [];
        if (in_array('Fecha', $encabezados_judicial)) {
            $header[] = 'Fecha';
            $w[] = 25;
        }
        if (in_array('Fecha Clave', $encabezados_judicial)) {
            $header[] = 'Fecha Clave';
            $w[] = 30;
        }
        if (in_array('Acto', $encabezados_judicial)) {
            $header[] = 'Acto';
            $w[] = 40;
        }
        if (in_array('Acción en Fecha Clave', $encabezados_judicial)) {
            $header[] = 'Acción en Fecha Clave';
            $w[] = 35;
        }
        if (in_array('Descripción', $encabezados_judicial)) {
            $header[] = 'Descripción';
            $w[] = 40;
        }

        foreach ($header as $i => $col) {
            $pdf->Cell($w[$i], 7, $col, 1, 0, 'C', 1);
        }
        $pdf->Ln();

        $pdf->SetFont('', '');
        $fill = 0;
        foreach ($judiciales as $judicial) {
            foreach ($header as $i => $col) {
                $pdf->Cell($w[$i], 7, $judicial[$i == 0 ? 'fecha_judicial' : ($i == 1 ? 'fecha_clave_judicial' : ($i == 2 ? 'acto_judicial' : ($i == 3 ? 'accion_en_fecha_clave' : 'descripcion_judicial')))], 1, 0, 'L', $fill);
            }
            $pdf->Ln();
            $fill = !$fill;
        }
        $pdf->Cell(array_sum($w), 0, '', 'T');
        $pdf->Ln(2);
    }

    $pdf->Ln(5);
}

// Clientes sin historial
if (!empty($clientes_sin_historial) && !empty($encabezados_sin_historial)) {
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Clientes sin Historial', 0, 1, 'L');
    $pdf->Ln(3);

    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(0.3);
    $pdf->SetFont('', 'B');

    // Encabezados de la tabla
    $header = [];
    $w = [];
    if (in_array('Nombres', $encabezados_sin_historial)) {
        $header[] = 'Nombre Completo';
        $w[] = 90;
    }
    if (in_array('DNI', $encabezados_sin_historial)) {
        $header[] = 'DNI';
        $w[] = 30;
    }

    foreach ($header as $i => $col) {
        $pdf->Cell($w[$i], 7, $col, 1, 0, 'C', 1);
    }
    $pdf->Ln();

    $pdf->SetFont('', '');
    $fill = 0;
    foreach ($clientes_sin_historial as $cliente) {
        foreach ($header as $i => $col) {
            $pdf->Cell($w[$i], 7, $i == 0 ? $cliente['nombre'] . ' ' . $cliente['apellidos'] : $cliente['dni'], 1, 0, 'L', $fill);
        }
        $pdf->Ln();
        $fill = !$fill;
    }
    $pdf->Cell(array_sum($w), 0, '', 'T');
    $pdf->Ln(5);
}

// Cerrar y generar el PDF
$pdf->Output('reporte_' . $mes . '_' . $anio . '.pdf', 'I');
?>
