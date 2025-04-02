<?php
require "connection.php";

$dni = isset($_GET['dni']) ? $_GET['dni'] : '';
$id_cliente = null;

if ($dni) {
    $sql = "SELECT id_cliente FROM clientes WHERE dni='$dni'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_cliente = $row['id_cliente'];
    }
}

echo json_encode(['id_cliente' => $id_cliente]);
$conn->close();
?>