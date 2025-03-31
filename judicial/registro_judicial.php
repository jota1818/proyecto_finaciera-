<?php
require "../conexion_db/connection.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establecer la fecha y hora actual
    $fecha = date('Y-m-d');

    // Datos de la etapa judicial
    $etapa = "Judicial"; // Establecer automáticamente como "Judicial"
    $acto = $_POST["acto"];
    $juzgado = $_POST["juzgado"];
    $n_exp_juzgado = $_POST["n_exp_juzgado"] ?? null; // Opcional
    $n_cedula = $_POST["n_cedula"] ?? null; // Opcional
    $descripcion = $_POST["descripcion"];
    $doc_evidencia = $_FILES["doc_evidencia"]["name"];
    $fecha_clave = $_POST["fecha_clave"];
    $accion_en_fecha_clave = $_POST["accion_en_fecha_clave"];
    $actor = $_POST["actor"];

    // Directorio de destino para los archivos subidos
    $target_dir = "uploads/";

    // Crear el directorio si no existe
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Mover archivos al directorio deseado
    move_uploaded_file($_FILES["doc_evidencia"]["tmp_name"], $target_dir . $doc_evidencia);

    // Insertar el nuevo registro
    $sql_insert = "INSERT INTO etapa_judicial (etapa, fecha, acto, juzgado, n_exp_juzgado, n_cedula, descripcion, doc_evidencia, fecha_clave, accion_en_fecha_clave, actor)
    VALUES ('$etapa', '$fecha', '$acto', '$juzgado', '$n_exp_juzgado', '$n_cedula', '$descripcion', '$target_dir$doc_evidencia', '$fecha_clave', '$accion_en_fecha_clave', '$actor')";

    if ($conn->query($sql_insert) === TRUE) {
        $message = '<div class="alert alert-success" role="alert">Registro exitoso</div>';
    } else {
        $message = '<div class="alert alert-danger" role="alert">Error: ' . $conn->error . '</div>';
    }

    echo $message;
}

$conn->close();
?>
