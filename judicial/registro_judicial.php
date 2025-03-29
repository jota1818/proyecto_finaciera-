<?php
date_default_timezone_set('America/Lima');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proyect";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

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



<!-- <!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="validacion_judicial.js" defer></script>
</head>

<body class="container mt-3">
    <div>
        <h2>Formulario de Etapa Judicial</h2>
        <?php if ($message): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="form-container">
        <form name="judicialForm" method="post" action="registro_judicial.php" enctype="multipart/form-data" onsubmit="return validarFormularioJudicial()">
            <div class="row">
                <div class="col-md-12 border p-3">
                    <h4>Información de la Etapa Judicial</h4>
                    <div class="mb-2">
                        <label class="fw-bold">Acto:</label>
                        <input type="text" name="acto" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Juzgado:</label>
                        <input type="text" name="juzgado" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Número de Expediente del Juzgado:</label>
                        <input type="text" name="n_exp_juzgado" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Número de Cédula:</label>
                        <input type="text" name="n_cedula" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Descripción:</label>
                        <textarea name="descripcion" required class="form-control"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Documento de Evidencia:</label>
                        <input type="file" name="doc_evidencia" accept=".docx, .pdf, .jpg, .jpeg, .png" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Fecha Clave:</label>
                        <input type="date" name="fecha_clave" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Acción en Fecha Clave:</label>
                        <input type="text" name="accion_en_fecha_clave" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Actor Involucrado:</label>
                        <input type="text" name="actor" required class="form-control">
                    </div>
                </div>
            </div>
            <div class="fixed-buttons">
                <button type="submit" class="btn btn-primary mt-3">Registrar</button>
                <button type="reset" class="btn btn-secondary mt-3">Limpiar</button>
                <br>
                <button type="button" class="btn btn-danger mt-3" onclick="window.location.href='../registro_cliente/index.php'">Salir</button>
            </div>
        </form>
    </div>
</body>

</html>
 -->