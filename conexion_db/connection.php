<?php
date_default_timezone_set('America/Lima');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "eneproyect_bd";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
