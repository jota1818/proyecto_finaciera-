<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proyect";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$dni = isset($_POST['dni']) ? $_POST['dni'] : '';
$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
$apellidos = isset($_POST['apellidos']) ? $_POST['apellidos'] : '';

$sql = "SELECT nombre, apellidos, dni, telefono, monto, saldo, fecha_clave, accion_fecha_clave FROM clientes WHERE 1=1";
if ($dni != '') {
    $sql .= " AND dni='$dni'";
}
if ($nombre != '') {
    $sql .= " AND nombre LIKE '%$nombre%'";
}
if ($apellidos != '') {
    $sql .= " AND apellidos LIKE '%$apellidos%'";
}
$sql .= " ORDER BY nombre ASC, apellidos ASC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
        echo "<td>" . htmlspecialchars($row['apellidos']) . "</td>";
        echo "<td>" . htmlspecialchars($row['dni']) . "</td>";
        echo "<td>" . htmlspecialchars($row['telefono']) . "</td>";
        echo "<td>" . htmlspecialchars($row['monto']) . "</td>";
        echo "<td>" . htmlspecialchars($row['saldo']) . "</td>";
        echo "<td>" . htmlspecialchars($row['fecha_clave']) . "</td>";
        echo "<td>" . htmlspecialchars($row['accion_fecha_clave']) . "</td>";
        echo "<td class='fixed-column'><button type='button' class='btn btn-warning' onclick='mostrarCliente(\"" . htmlspecialchars($row['dni']) . "\")'>Seleccionar</button></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No se encontraron clientes</td></tr>";
}

$conn->close();
?>