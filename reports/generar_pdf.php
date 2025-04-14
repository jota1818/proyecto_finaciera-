<?php
require_once '../tcpdf/tcpdf.php';
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

// Establecer la fuente
$pdf->SetFont('helvetica', '', 10);

// Agregar el título del reporte
$pdf->Cell(0, 10, 'Reporte de Historiales - ' . date('F Y', strtotime($fecha_inicio)), 0, 1, 'C');

// Agregar los datos de los clientes
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

    // Agregar el nombre del cliente
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Cliente: ' . $cliente['nombre'] . ' ' . $cliente['apellidos'], 0, 1, 'L');
    $pdf->Ln(5);

    // Agregar el historial pre-judicial
    if (!empty($prejudiciales)) {
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 10, 'Historial Pre-Judicial', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);

        // Crear una tabla para el historial pre-judicial
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        $pdf->SetFont('', 'B');

        // Encabezados de la tabla
        $header = array('Fecha Acto', 'Acto', 'Descripción', 'Fecha Clave', 'Acción en Fecha Clave');
        $w = array(30, 40, 60, 30, 40);
        $num_headers = count($header);

        for ($i = 0; $i < $num_headers; ++$i) {
            $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $pdf->Ln();

        // Filas de la tabla
        $pdf->SetFont('', '');
        $fill = 0;
        foreach ($prejudiciales as $prejudicial) {
            $pdf->Cell($w[0], 6, $prejudicial['fecha_acto'], 'LR', 0, 'L', $fill);
            $pdf->Cell($w[1], 6, $prejudicial['acto'], 'LR', 0, 'L', $fill);
            $pdf->Cell($w[2], 6, $prejudicial['descripcion'], 'LR', 0, 'L', $fill);
            $pdf->Cell($w[3], 6, $prejudicial['fecha_clave'], 'LR', 0, 'L', $fill);
            $pdf->Cell($w[4], 6, $prejudicial['accion_fecha_clave'], 'LR', 0, 'L', $fill);
            $pdf->Ln();
            $fill = !$fill;
        }
        $pdf->Cell(array_sum($w), 0, '', 'T');
        $pdf->Ln(5);
    }

    // Agregar el historial judicial
    if (!empty($judiciales)) {
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 10, 'Historial Judicial', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);

        // Crear una tabla para el historial judicial
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        $pdf->SetFont('', 'B');

        // Encabezados de la tabla
        $header = array('Fecha Judicial', 'Acto', 'Descripción', 'Fecha Clave', 'Acción en Fecha Clave');
        $w = array(30, 40, 60, 30, 40);
        $num_headers = count($header);

        for ($i = 0; $i < $num_headers; ++$i) {
            $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $pdf->Ln();

        // Filas de la tabla
        $pdf->SetFont('', '');
        $fill = 0;
        foreach ($judiciales as $judicial) {
            $pdf->Cell($w[0], 6, $judicial['fecha_judicial'], 'LR', 0, 'L', $fill);
            $pdf->Cell($w[1], 6, $judicial['acto_judicial'], 'LR', 0, 'L', $fill);
            $pdf->Cell($w[2], 6, $judicial['descripcion_judicial'], 'LR', 0, 'L', $fill);
            $pdf->Cell($w[3], 6, $judicial['fecha_clave_judicial'], 'LR', 0, 'L', $fill);
            $pdf->Cell($w[4], 6, $judicial['accion_en_fecha_clave'], 'LR', 0, 'L', $fill);
            $pdf->Ln();
            $fill = !$fill;
        }
        $pdf->Cell(array_sum($w), 0, '', 'T');
        $pdf->Ln(5);
    }

    // Agregar un espacio entre clientes
    $pdf->Ln(10);
}

// Cerrar y generar el PDF
$pdf->Output('reporte_' . $mes . '_' . $anio . '.pdf', 'D');
?>
