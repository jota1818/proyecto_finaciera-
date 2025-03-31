<?php
require "../conexion_db/connection.php";

if (isset($_GET['dni'])) {
    $dni = $_GET['dni'];
    $sql_check = "SELECT * FROM clientes WHERE dni='$dni'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        echo "exists";
    } else {
        echo "unique";
    }
}

$conn->close();
?>