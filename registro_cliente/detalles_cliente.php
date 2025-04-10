<?php
require "../conexion_db/connection.php";

function formatNumber($number) {
    return 'S/.' . number_format($number, 2, '.', ',');
}

$dni = isset($_GET['dni']) ? $_GET['dni'] : '';
$sql = "SELECT * FROM clientes WHERE dni='$dni'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    echo '<div class="row">';

    // Información del Titular
    echo '<div class="col-md-6">';
    echo "<div class='sticky-top bg-light p-2 mb-3'>"; // Fijar el título en la parte superior
    echo "<h4 class='titular-title'>Titular</h4>"; // Añade la clase titular-title
    echo "</div>";
    echo "<table class='table table-bordered'>";
    echo "<tr><td><strong>Nombre:</strong></td><td>" . htmlspecialchars($row['nombre'] . " " . $row['apellidos']) . "</td></tr>";
    echo "<tr><td><strong>DNI:</strong></td><td>" . htmlspecialchars($row['dni']) . "</td></tr>";
    echo "<tr><td><strong>Teléfono:</strong></td><td>" . htmlspecialchars($row['telefono']) . "</td></tr>";
    echo "<tr><td><strong>Fecha de Nacimiento:</strong></td><td>" . htmlspecialchars($row['fecha_nacimiento']) . "</td></tr>";
    echo "<tr><td><strong>Domicilio 1:</strong></td><td>" . htmlspecialchars($row['domicilio1']) . "</td></tr>";
    echo "<tr><td><strong>Referencia 1:</strong></td><td>" . htmlspecialchars($row['referencia1']) . "</td></tr>";
    if (!empty($row['domicilio2'])) {
        echo "<tr><td><strong>Domicilio 2:</strong></td><td>" . htmlspecialchars($row['domicilio2']) . "</td></tr>";
    }
    if (!empty($row['referencia2'])) {
        echo "<tr><td><strong>Referencia 2:</strong></td><td>" . htmlspecialchars($row['referencia2']) . "</td></tr>";
    }
    echo "<tr><td><strong>Ocupación:</strong></td><td>" . htmlspecialchars($row['ocupacion']) . "</td></tr>";
    echo "<tr><td><strong>Clasificación de Riesgo:</strong></td><td>" . htmlspecialchars($row['clasificacion_riesgo']) . "</td></tr>";
    echo "<tr><td><strong>Agencia:</strong></td><td>" . htmlspecialchars($row['agencia']) . "</td></tr>";
    echo "<tr><td><strong>Tipo de credito:</strong></td><td>" . htmlspecialchars($row['tipo_credito']) . "</td></tr>";
    echo "<tr><td><strong>Estado:</strong></td><td>" . htmlspecialchars($row['estado']) . "</td></tr>";
    echo "<tr><td><strong>Fecha de Desembolso:</strong></td><td>" . htmlspecialchars($row['fecha_desembolso']) . "</td></tr>";
    echo "<tr><td><strong>Fecha de Vencimiento:</strong></td><td>" . htmlspecialchars($row['fecha_vencimiento']) . "</td></tr>";
    echo "<tr><td><strong>Monto:</strong></td><td>" . formatNumber($row['monto']) . "</td></tr>";
    echo "<tr><td><strong>Saldo:</strong></td><td>" . formatNumber($row['saldo']) . "</td></tr>";
    echo "<tr><td><strong>Fecha clave:</strong></td><td>" . htmlspecialchars($row['fecha_clave']) . "</td></tr>";
    echo "<tr><td><strong>Acción en fecha clave:</strong></td><td>" . htmlspecialchars($row['accion_fecha_clave']) . "</td></tr>";
    echo "</table>";
    echo '</div>';

    // Información del Aval
    echo '<div class="col-md-6">';
    echo "<div class='sticky-top bg-light p-2 mb-3'>"; // Fijar el título en la parte superior
    echo "<h4 class='aval-title'>Aval</h4>"; // Añade la clase aval-title
    echo "</div>";
    echo "<table class='table table-bordered'>";
    if (!empty($row['nombre_garante']) || !empty($row['apellidos_garante'])) {
        echo "<tr><td><strong>Nombre:</strong></td><td>" . htmlspecialchars($row['nombre_garante'] . " " . $row['apellidos_garante']) . "</td></tr>";
    }
    if (!empty($row['dni_garante'])) {
        echo "<tr><td><strong>DNI:</strong></td><td>" . htmlspecialchars($row['dni_garante']) . "</td></tr>";
    }
    if (!empty($row['telefono_garante'])) {
        echo "<tr><td><strong>Teléfono:</strong></td><td>" . htmlspecialchars($row['telefono_garante']) . "</td></tr>";
    }
    if ($row['fecha_nacimiento_garante'] !== '0000-00-00' && !empty($row['fecha_nacimiento_garante'])) {
        echo "<tr><td><strong>Fecha de Nacimiento:</strong></td><td>" . htmlspecialchars($row['fecha_nacimiento_garante']) . "</td></tr>";
    }
    if (!empty($row['domicilio1_garante'])) {
        echo "<tr><td><strong>Domicilio 1:</strong></td><td>" . htmlspecialchars($row['domicilio1_garante']) . "</td></tr>";
    }
    if (!empty($row['referencia1_garante'])) {
        echo "<tr><td><strong>Referencia 1:</strong></td><td>" . htmlspecialchars($row['referencia1_garante']) . "</td></tr>";
    }
    if (!empty($row['domicilio2_garante'])) {
        echo "<tr><td><strong>Domicilio 2:</strong></td><td>" . htmlspecialchars($row['domicilio2_garante']) . "</td></tr>";
    }
    if (!empty($row['referencia2_garante'])) {
        echo "<tr><td><strong>Referencia 2:</strong></td><td>" . htmlspecialchars($row['referencia2_garante']) . "</td></tr>";
    }
    if (!empty($row['ocupacion_garante'])) {
        echo "<tr><td><strong>Ocupación:</strong></td><td>" . htmlspecialchars($row['ocupacion_garante']) . "</td></tr>";
    }
    if (!empty($row['clasificacion_riesgo_garante'])) {
        echo "<tr><td><strong>Clasificación de Riesgo:</strong></td><td>" . htmlspecialchars($row['clasificacion_riesgo_garante']) . "</td></tr>";
    }
    echo "</table>";
    echo '</div>';

    echo '</div>';
} else {
    echo "No se encontraron detalles para este cliente.";
}

$conn->close();
?>
