<?php
require_once '../tcpdf/tcpdf.php';
require "../conexion_db/connection.php";

function formatDate($date) {
    return date('d-m-Y', strtotime($date));
}

// Obtener parámetros del formulario
$mes = isset($_POST['mes']) ? $_POST['mes'] : date('m');
$anio = isset($_POST['anio']) ? $_POST['anio'] : date('Y');
$filtro = isset($_POST['filtro']) ? $_POST['filtro'] : 'todos'; // Nuevo filtro
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
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 10, 'Reporte de Historiales - ' . $meses[$mes] . ' ' . $anio, 0, 1, 'C');
$pdf->Ln(5);

// Agregar los datos de los clientes según el filtro seleccionado
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

if ($filtro === 'con_historial') {
    // Mostrar solo clientes con historial
    foreach ($clientes_prejudicial as $id_cliente => $prejudiciales) {
        $cliente = $prejudiciales[0];
        $judiciales = $clientes_judicial[$id_cliente] ?? [];
        agregarClienteAlPDF($pdf, $cliente, $prejudiciales, $judiciales, $encabezados_prejudicial, $encabezados_judicial);
    }
} elseif ($filtro === 'sin_historial') {
    // Mostrar solo clientes sin historial
    agregarClientesSinHistorialAlPDF($pdf, $clientes_sin_historial, $encabezados_sin_historial);
} else {
    // Mostrar todos los clientes
    foreach ($clientes_prejudicial as $id_cliente => $prejudiciales) {
        $cliente = $prejudiciales[0];
        $judiciales = $clientes_judicial[$id_cliente] ?? [];
        agregarClienteAlPDF($pdf, $cliente, $prejudiciales, $judiciales, $encabezados_prejudicial, $encabezados_judicial);
    }
    agregarClientesSinHistorialAlPDF($pdf, $clientes_sin_historial, $encabezados_sin_historial);
}

// Cerrar y generar el PDF
$pdf->Output('reporte_' . $mes . '_' . $anio . '.pdf', 'I');

function agregarClienteAlPDF($pdf, $cliente, $prejudiciales, $judiciales, $encabezados_prejudicial, $encabezados_judicial)
{
    // Agregar el nombre del cliente
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 10, 'Cliente: ' . $cliente['nombre'] . ' ' . $cliente['apellidos'], 0, 1, 'L');
    $pdf->Ln(-2);

    // Agregar el historial pre-judicial
    if (!empty($prejudiciales) && !empty($encabezados_prejudicial)) {
        agregarTablaAlPDF($pdf, 'Etapa Pre-Judicial', $prejudiciales, $encabezados_prejudicial);
    }

    // Agregar el historial judicial
    if (!empty($judiciales) && !empty($encabezados_judicial)) {
        agregarTablaAlPDF($pdf, 'Etapa Judicial', $judiciales, $encabezados_judicial);
    }

    $pdf->Ln(5);
}

function agregarClientesSinHistorialAlPDF($pdf, $clientes_sin_historial, $encabezados_sin_historial)
{
    if (!empty($clientes_sin_historial) && !empty($encabezados_sin_historial)) {
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Clientes sin Historial', 0, 1, 'L');
        $pdf->Ln(3);

        agregarTablaAlPDF($pdf, 'Datos del cliente', $clientes_sin_historial, $encabezados_sin_historial);
    }
}

function agregarTablaAlPDF($pdf, $titulo, $datos, $encabezados, )
{
    // Verificar si hay espacio suficiente para el título y al menos una fila
    if ($pdf->GetY() > $pdf->getPageHeight() - 30) { // 30mm margin from bottom
        $pdf->AddPage();
    }

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(0, 10, $titulo, 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 8);

    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(0.3);

    $header = [];
    $w = [];

    if (in_array('Fecha', $encabezados)) {
        $header[] = 'Fecha';
        $w[] = 20;
    }
    if (in_array('Fecha Clave', $encabezados)) {
        $header[] = 'Fecha Clave';
        $w[] = 23;
    }
    if (in_array('Acto', $encabezados)) {
        $header[] = 'Acto';
        $w[] = 32;
    }
    if (in_array('Acción en Fecha Clave', $encabezados)) {
        $header[] = 'Acción en Fecha Clave';
        $w[] = 42;
    }
    if (in_array('Descripción', $encabezados)) {
        $header[] = 'Descripción';
        $w[] = 50;
    }
    if (in_array('Objetivo Logrado', $encabezados)) {
        $header[] = 'Objetivo Logrado';
        $w[] = 28;
    }
    if (in_array('Nombres', $encabezados)) {
        $header[] = 'Nombre Completo';
        $w[] = 90;
    }
    if (in_array('DNI', $encabezados)) {
        $header[] = 'DNI';
        $w[] = 30;
    }

    // Encabezado de tabla
    $pdf->SetFont('', 'B');
    foreach ($header as $i => $col) {
        $pdf->MultiCell($w[$i], 7, $col, 1, 'C', 1, 0);
    }
    $pdf->Ln();

    $pdf->SetFont('', '');
    $fill = 0;

    foreach ($datos as $row) {
        $rowData = [];
        foreach ($header as $col) {
            $value = '';
            if ($col == 'Fecha') {
                $value = formatDate($row['fecha_acto'] ?? ($row['fecha_judicial'] ?? ''));
            } elseif ($col == 'Fecha Clave') {
                $value = formatDate($row['fecha_clave'] ?? ($row['fecha_clave_judicial'] ?? ''));
            } elseif ($col == 'Acto') {
                $value = $row['acto'] ?? ($row['acto_judicial'] ?? '');
            } elseif ($col == 'Acción en Fecha Clave') {
                $value = $row['accion_fecha_clave'] ?? ($row['accion_en_fecha_clave'] ?? '');
            } elseif ($col == 'Descripción') {
                $value = $row['descripcion'] ?? ($row['descripcion_judicial'] ?? '');
            } elseif ($col == 'Objetivo Logrado') {
                $value = $row['objetivo_logrado'] ?? '';
            } elseif ($col == 'Nombre Completo') {
                $value = $row['nombre'] . ' ' . $row['apellidos'];
            } elseif ($col == 'DNI') {
                $value = $row['dni'] ?? '';
            }
            $rowData[] = $value;
        }

        // Calcular la altura máxima de la fila
        $maxHeight = 0;
        foreach ($rowData as $i => $txt) {
            $nb = $pdf->getNumLines($txt, $w[$i]);
            $height = 6 * $nb;
            if ($height > $maxHeight) $maxHeight = $height;
        }

        // Verificar si la fila cabe en la página actual
        if ($pdf->GetY() + $maxHeight > $pdf->getPageHeight() - 20) { // 20mm de margen desde abajo
            $pdf->AddPage();
            // Volver a dibujar los encabezados en la nueva página
            $pdf->SetFont('', 'B');
            foreach ($header as $i => $col) {
                $pdf->MultiCell($w[$i], 7, $col, 1, 'C', 1, 0);
            }
            $pdf->Ln();
            $pdf->SetFont('', '');
        }

        // Dibujar las celdas de la fila
        foreach ($rowData as $i => $txt) {
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $align = ($header[$i] == 'Objetivo Logrado') ? 'C' : 'L'; // Centrar solo la columna "Objetivo Logrado"$pdf->MultiCell($w[$i], $maxHeight, $txt, 1, 'L', $fill, 0, '', '', true, 0, false, true, $maxHeight, 'M');
            $pdf->MultiCell($w[$i], $maxHeight, $txt, 1, $align, $fill, 0, '', '', true, 0, false, true, $maxHeight, 'M');
            $pdf->SetXY($x + $w[$i], $y);
        }
        $pdf->Ln();
        $fill = !$fill;
    }

    $pdf->Ln(5); // Espacio adicional después de la tabla
}
?>
