<?php
require "../conexion_db/connection.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establecer la fecha y hora actual
    $fecha_judicial = date('Y-m-d');
    $id_cliente = isset($_POST['id_cliente']) ? $_POST['id_cliente'] : '';

    // Datos de la etapa judicial
    $etapa = "Judicial"; // Establecer automáticamente como "Judicial"
    $acto_judicial = $_POST["acto_judicial"];
    $juzgado = $_POST["juzgado"];
    $n_exp_juzgado = $_POST["n_exp_juzgado"] ?? null; // Opcional
    $n_cedula = $_POST["n_cedula"] ?? null; // Opcional
    $descripcion_judicial = $_POST["descripcion_judicial"];
    $doc_evidencia = $_FILES["doc_evidencia"]["name"];
    $fecha_clave_judicial = $_POST["fecha_clave_judicial"];
    $accion_en_fecha_clave = $_POST["accion_en_fecha_clave"];
    $actor_judicial = $_POST["actor_judicial"];

    // Validar fecha clave
    $fecha_clave_datetime = new DateTime($fecha_clave_judicial);
    $fecha_actual = new DateTime();
    if ($fecha_clave_datetime < $fecha_actual) {
        $message = '<div class="alert alert-danger" role="alert">La fecha clave no puede ser anterior a la fecha actual.</div>';
        echo $message;
        exit;
    }

    // Directorio de destino para los archivos subidos
    $target_dir = "uploads/";

    // Crear el directorio si no existe
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Mover archivos al directorio deseado
    move_uploaded_file($_FILES["doc_evidencia"]["tmp_name"], $target_dir . $doc_evidencia);

    // Insertar el nuevo registro
    $sql_insert = "INSERT INTO etapa_judicial (id_cliente, etapa, fecha_judicial, acto_judicial, juzgado, n_exp_juzgado, n_cedula, descripcion_judicial, doc_evidencia, fecha_clave_judicial, accion_en_fecha_clave, actor_judicial)
    VALUES ('$id_cliente', '$etapa', '$fecha_judicial', '$acto_judicial', '$juzgado', '$n_exp_juzgado', '$n_cedula', '$descripcion_judicial', '$target_dir$doc_evidencia', '$fecha_clave_judicial', '$accion_en_fecha_clave', '$actor_judicial')";

    if ($conn->query($sql_insert) === TRUE) {
        $message = '<div class="alert alert-success" role="alert">Registro exitoso</div>';
    } else {
        $message = '<div class="alert alert-danger" role="alert">Error: ' . $conn->error . '</div>';
    }

    echo $message;
}

$conn->close();
?>
