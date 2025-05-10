<?php
require "../conexion_db/connection.php";

// Función para formatear montos con dos decimales
function formatNumber($number) {
    return 'S/.' . number_format($number, 2, '.', ',');
}

// Función para formatear fechas al formato d-m-Y
function formatDate($date) {
    return date('d-m-Y', strtotime($date));
}

// Obtener DNI desde la URL
$dni = isset($_GET['dni']) ? $_GET['dni'] : '';

// Consulta para obtener los datos del cliente
$sql = "SELECT * FROM clientes WHERE dni='$dni'";
$result = $conn->query($sql);

// Si hay resultados
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    echo '<div class="row">';

    // Tabla con datos del titular y aval
    echo '<div class="col-md-8">';
    echo '<table class="table table-bordered">';
    echo '<thead>';
    echo '<tr>';
    echo '<th></th>';
    echo '<th>Titular</th>';
    echo '<th>Aval</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Fila: Nombre completo
    echo '<tr><td><strong>Nombre:</strong></td><td>' . htmlspecialchars($row['nombre'] . " " . $row['apellidos']) . '</td><td>' . htmlspecialchars($row['nombre_garante'] . " " . $row['apellidos_garante']) . '</td></tr>';

    // Fila: DNI y Fecha de Nacimiento con cuadrado separado
    echo '<tr><td><strong>DNI - Fec. Nacim:</strong></td>
    <td>
        <table class="table table-bordered m-0">
            <tr>
                <td>' . htmlspecialchars($row['dni']) . '</td>
                <td>' . formatDate($row['fecha_nacimiento']) . '</td>
            </tr>
        </table>
    </td>
    <td>
        <table class="table table-bordered m-0">
            <tr>
                <td>' . htmlspecialchars($row['dni_garante']) . '</td>
                <td>' . formatDate($row['fecha_nacimiento_garante']) . '</td>
            </tr>
        </table>
    </td>
    </tr>';

    // Fila: Teléfonos
    echo '<tr><td><strong>Teléfono:</strong></td><td>' . htmlspecialchars($row['telefono']) . '</td><td>' . htmlspecialchars($row['telefono_garante']) . '</td></tr>';
    // Fila: Domicilio 1
    echo '<tr><td><strong>Domicilio 1:</strong></td><td>' . htmlspecialchars($row['domicilio1']) . '</td><td>' . htmlspecialchars($row['domicilio1_garante']) . '</td></tr>';
    // Fila: Referencia 1
    echo '<tr><td><strong>Referencia 1:</strong></td><td>' . htmlspecialchars($row['referencia1']) . '</td><td>' . htmlspecialchars($row['referencia1_garante']) . '</td></tr>';
    // Fila: Domicilio 2
    echo '<tr><td><strong>Domicilio 2:</strong></td><td>' . htmlspecialchars($row['domicilio2']) . '</td><td>' . htmlspecialchars($row['domicilio2_garante']) . '</td></tr>';
    // Fila: Referencia 2
    echo '<tr><td><strong>Referencia 2:</strong></td><td>' . htmlspecialchars($row['referencia2']) . '</td><td>' . htmlspecialchars($row['referencia2_garante']) . '</td></tr>';
    // Fila: Ocupación y Clasificación de Riesgo con celdas separadas
    echo '<tr><td><strong>Ocup. - C. Riesgo:</strong></td>
    <td>
        <table class="table table-bordered m-0">
            <tr>
                <td>' . htmlspecialchars($row['ocupacion']) . '</td>
                <td>' . htmlspecialchars($row['clasificacion_riesgo']) . '</td>
            </tr>
        </table>
    </td>
    <td>
        <table class="table table-bordered m-0">
            <tr>
                <td>' . htmlspecialchars($row['ocupacion_garante']) . '</td>
                <td>' . htmlspecialchars($row['clasificacion_riesgo_garante']) . '</td>
            </tr>
        </table>
    </td>
    </tr>';

    echo '</tbody>';
    echo '</table>';
    echo '</div>';

    // Tabla con información del crédito en la parte derecha
    echo '<div class="col-md-4">';
    echo '<table class="table table-bordered">';
    echo '<tr><td><strong>Agencia:</strong></td><td>' . htmlspecialchars($row['agencia']) . '</td></tr>';
    echo '<tr><td><strong>Tipo de Crédito:</strong></td><td>' . htmlspecialchars($row['tipo_credito']) . '</td></tr>';
    echo '<tr><td><strong>Estado:</strong></td><td>' . htmlspecialchars($row['estado']) . '</td></tr>';
    echo '<tr><td><strong>F. Desembolso:</strong></td><td>' . formatDate($row['fecha_desembolso']) . '</td></tr>';
    echo '<tr><td><strong>F. Vencimiento:</strong></td><td>' . formatDate($row['fecha_vencimiento']) . '</td></tr>';
    echo '<tr><td><strong>Monto:</strong></td><td>' . formatNumber($row['monto']) . '</td></tr>';
    echo '<tr><td><strong>Saldo:</strong></td><td>' . formatNumber($row['saldo']) . '</td></tr>';
    echo '<tr><td><strong>Fecha Clave:</strong></td><td>' . formatDate($row['fecha_clave']) . '</td></tr>';
    echo '<tr><td><strong>Acción F. Clave:</strong></td><td>' . htmlspecialchars($row['accion_fecha_clave']) . '</td></tr>';
    echo '</table>';
    echo '</div>';

    echo '</div>'; // Fin de la fila principal

} else {
    // Si no se encuentra ningún cliente
    echo '<div class="no-results text-center d-flex justify-content-center align-items-center">';
    echo 'No se encontraron detalles para este cliente.';
    echo '</div>';
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
