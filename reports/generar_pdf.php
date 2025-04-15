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

// Consulta para obtener todos los clientes
$sql_clientes = "SELECT * FROM clientes ORDER BY nombre, apellidos";
$result_clientes = $conn->query($sql_clientes);

$conn->close();

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

    // Agregar el nombre del cliente
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Cliente: ' . $cliente['nombre'] . ' ' . $cliente['apellidos'], 0, 1, 'L');
    $pdf->Ln(5);

    // Agregar el historial pre-judicial
    if (!empty($prejudiciales)) {
        $pdf->SetFont('helvetica', 'B', 8); // Reducir el tamaño de la fuente de los encabezados
        $pdf->Cell(0, 10, 'Historial Pre-Judicial', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 8); // Reducir el tamaño de la fuente del contenido

        // Crear una tabla para el historial pre-judicial
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        $pdf->SetFont('', 'B');

        // Encabezados de la tabla
        $header = array('Fecha', 'Fecha Clave', 'Acto', 'Acción en Fecha Clave', 'Descripción', 'Objetivo logrado');
        $w = array(25, 25, 30, 40, 45, 30); // Ajustar el ancho de las columnas
        $num_headers = count($header);

        for ($i = 0; $i < $num_headers; ++$i) {
            $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $pdf->Ln();

        // Filas de la tabla
        $pdf->SetFont('', '');
        $fill = 0;
        foreach ($prejudiciales as $prejudicial) {
            $lineHeight = 5; // altura de línea para multilinea
            $maxY = $pdf->GetY();

            // Calculamos la altura máxima de celda para esta fila
            $heights = [];
            $heights[] = $pdf->getStringHeight($w[0], $prejudicial['fecha_acto']);
            $heights[] = $pdf->getStringHeight($w[1], $prejudicial['fecha_clave']);
            $heights[] = $pdf->getStringHeight($w[2], $prejudicial['acto']);
            $heights[] = $pdf->getStringHeight($w[3], $prejudicial['accion_fecha_clave']);
            $heights[] = $pdf->getStringHeight($w[4], $prejudicial['descripcion']);
            $heights[] = $pdf->getStringHeight($w[5], $prejudicial['objetivo_logrado']);
            $rowHeight = max($heights);

            $pdf->MultiCell($w[0], $rowHeight, $prejudicial['fecha_acto'], 1, 'C', $fill, 0); // centrado
            $pdf->MultiCell($w[1], $rowHeight, $prejudicial['fecha_clave'], 1, 'C', $fill, 0); // centrado
            $pdf->MultiCell($w[2], $rowHeight, $prejudicial['acto'], 1, 'L', $fill, 0);
            $pdf->MultiCell($w[3], $rowHeight, $prejudicial['accion_fecha_clave'], 1, 'L', $fill, 0);
            $pdf->MultiCell($w[4], $rowHeight, $prejudicial['descripcion'], 1, 'L', $fill, 0);
            $pdf->MultiCell($w[5], $rowHeight, $prejudicial['objetivo_logrado'], 1, 'C', $fill, 1); // centrado


            $fill = !$fill;
        }
        $pdf->Cell(array_sum($w), 0, '', 'T');
        $pdf->Ln(5);
    }

    // Agregar el historial judicial
    if (!empty($judiciales)) {
        $pdf->SetFont('helvetica', 'B', 8); // Reducir el tamaño de la fuente de los encabezados
        $pdf->Cell(0, 10, 'Historial Judicial', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 8); // Reducir el tamaño de la fuente del contenido

        // Crear una tabla para el historial judicial
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        $pdf->SetFont('', 'B');

        // Encabezados de la tabla
        $header = array('Fecha', 'Fecha Clave', 'Acto', 'Acción en Fecha Clave', 'Descripción');
        $w = array(25, 30, 40, 35, 40); // Ajustar el ancho de las columnas
        $num_headers = count($header);

        for ($i = 0; $i < $num_headers; ++$i) {
            $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $pdf->Ln();

        // Filas de la tabla
        $pdf->SetFont('', '');
        $fill = 0;
        foreach ($judiciales as $judicial) {
            $heights = [];
            $heights[] = $pdf->getStringHeight($w[0], $judicial['fecha_judicial']);
            $heights[] = $pdf->getStringHeight($w[1], $judicial['fecha_clave_judicial']);
            $heights[] = $pdf->getStringHeight($w[2], $judicial['acto_judicial']);
            $heights[] = $pdf->getStringHeight($w[3], $judicial['accion_en_fecha_clave']);
            $heights[] = $pdf->getStringHeight($w[4], $judicial['descripcion_judicial']);
            $rowHeight = max($heights);

            $pdf->MultiCell($w[0], $rowHeight, $judicial['fecha_judicial'], 1, 'C', $fill, 0); // centrado
            $pdf->MultiCell($w[1], $rowHeight, $judicial['fecha_clave_judicial'], 1, 'C', $fill, 0); // centrado
            $pdf->MultiCell($w[2], $rowHeight, $judicial['acto_judicial'], 1, 'L', $fill, 0);
            $pdf->MultiCell($w[3], $rowHeight, $judicial['accion_en_fecha_clave'], 1, 'L', $fill, 0);
            $pdf->MultiCell($w[4], $rowHeight, $judicial['descripcion_judicial'], 1, 'C', $fill, 1); // centrado



            $fill = !$fill;
        }
        $pdf->Cell(array_sum($w), 0, '', 'T');
        $pdf->Ln(5);
    }

    // Agregar un espacio entre clientes
    $pdf->Ln(10);
}

// Clientes sin historial
if (!empty($clientes_sin_historial)) {
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Clientes sin Historial', 0, 1, 'L');
    $pdf->Ln(5);

    $pdf->SetFont('helvetica', 'B', 8); // Reducir el tamaño de la fuente de los encabezados
    $pdf->Cell(0, 10, 'Clientes sin Historial', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 8); // Reducir el tamaño de la fuente del contenido

    // Crear una tabla para los clientes sin historial
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(0.3);
    $pdf->SetFont('', 'B');

    // Encabezados de la tabla
    $header = array('Nombre Completo', 'DNI');
    $w = array(90, 30); // Ajustar el ancho de las columnas
    $num_headers = count($header);

    for ($i = 0; $i < $num_headers; ++$i) {
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
    }
    $pdf->Ln();

    // Filas de la tabla
    $pdf->SetFont('', '');
    $fill = 0;
    foreach ($clientes_sin_historial as $cliente) {
        $pdf->Cell($w[0], 6, $cliente['nombre'] . ' ' . $cliente['apellidos'], 'LR', 0, 'L', $fill);
        $pdf->Cell($w[1], 6, $cliente['dni'], 'LR', 0, 'L', $fill);
        $pdf->Ln();
        $fill = !$fill;
    }
    $pdf->Cell(array_sum($w), 0, '', 'T');
    $pdf->Ln(5);
}

// Cerrar y generar el PDF
$pdf->Output('reporte_' . $mes . '_' . $anio . '.pdf', 'D');
/* esto es la correcta por el momento */