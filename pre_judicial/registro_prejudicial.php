<?php
date_default_timezone_set('America/Lima'); // Asegúrate de configurar tu zona horaria local

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
    $fecha_acto = date('Y-m-d H:i:s');

    // Datos de la etapa pre-judicial
    $acto = $_POST["acto"];
    $n_de_notif_voucher = $_POST["n_de_notif_voucher"];
    $descripcion = $_POST["descripcion"];
    $notif_compromiso_pago_evidencia = $_FILES["notif_compromiso_pago_evidencia"]["name"];
    $fecha_clave = $_POST["fecha_clave"];
    $accion_fecha_clave = $_POST["accion_fecha_clave"];
    $actor = $_POST["actor"];
    $evidencia1_localizacion = $_FILES["evidencia1_localizacion"]["name"];
    $evidencia2_foto_fecha = $_FILES["evidencia2_foto_fecha"]["name"];
    $dias_de_mora = $_POST["dias_de_mora"];
    $dias_mora_PJ = $_POST["dias_mora_PJ"];
    $interes = $_POST["interes"];
    $saldo_int = $_POST["saldo_int"];
    $monto_amortizado = $_POST["monto_amortizado"];
    $saldo_fecha = $_POST["saldo_fecha"];

    // Directorio de destino para los archivos subidos
    $target_dir = "uploads/";

    // Crear el directorio si no existe
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Mover archivos al directorio deseado
    move_uploaded_file($_FILES["notif_compromiso_pago_evidencia"]["tmp_name"], $target_dir . $notif_compromiso_pago_evidencia);
    move_uploaded_file($_FILES["evidencia1_localizacion"]["tmp_name"], $target_dir . $evidencia1_localizacion);
    move_uploaded_file($_FILES["evidencia2_foto_fecha"]["tmp_name"], $target_dir . $evidencia2_foto_fecha);

    // Insertar el nuevo registro
    $sql_insert = "INSERT INTO etapa_prejudicial (fecha_acto, acto, n_de_notif_voucher, descripcion, notif_compromiso_pago_evidencia, fecha_clave, accion_fecha_clave, actor, evidencia1_localizacion, evidencia2_foto_fecha, dias_de_mora, dias_mora_PJ, interes, saldo_int, monto_amortizado, saldo_fecha)
    VALUES ('$fecha_acto', '$acto', '$n_de_notif_voucher', '$descripcion', '$target_dir$notif_compromiso_pago_evidencia', '$fecha_clave', '$accion_fecha_clave', '$actor', '$target_dir$evidencia1_localizacion', '$target_dir$evidencia2_foto_fecha', '$dias_de_mora', '$dias_mora_PJ', '$interes', '$saldo_int', '$monto_amortizado', '$saldo_fecha')";

    if ($conn->query($sql_insert) === TRUE) {
        // Obtener el ID del último registro insertado
        $last_id = $conn->insert_id;

        // Actualizar la fila anterior si existe
        actualizarFilaAnterior($conn, $last_id, $fecha_acto);

        $message = "Registro exitoso";
    } else {
        $message = "Error: " . $sql_insert . "<br>" . $conn->error;
    }
}

function actualizarFilaAnterior($conn, $last_id, $fecha_acto) {
    // Obtener la fila anterior
    $sql_select = "SELECT id, fecha_clave FROM etapa_prejudicial WHERE id < $last_id ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql_select);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_anterior = $row['id'];
        $fecha_clave_anterior = $row['fecha_clave'];

        // Calcular "Días desde Fecha Clave"
        $dias_desde_fecha_clave = calcularDiasDesdeFechaClave($fecha_acto, $fecha_clave_anterior);
        $objetivo_logrado = $dias_desde_fecha_clave >= 0 ? "NO" : "SI";

        // Actualizar la fila anterior
        $sql_update = "UPDATE etapa_prejudicial SET dias_desde_fecha_clave = $dias_desde_fecha_clave, objetivo_logrado = '$objetivo_logrado' WHERE id = $id_anterior";
        $conn->query($sql_update);
    }
}

function calcularDiasDesdeFechaClave($fecha_acto_siguiente, $fecha_clave) {
    $fecha_acto_siguiente = new DateTime($fecha_acto_siguiente);
    $fecha_clave = new DateTime($fecha_clave);

    // Calcular la diferencia de días
    $interval = $fecha_acto_siguiente->diff($fecha_clave);

    // Obtener la diferencia en días
    $dias = $interval->days;

    // Ajustar el signo de la diferencia
    if ($fecha_clave > $fecha_acto_siguiente) {
        $dias = -$dias-1;
    }

    return $dias;
}

$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="validacion_prejudicial.js" defer></script>
</head>
<body class="container mt-3">
    <div>
        <h2>Formulario de Etapa Pre-Judicial</h2>
        <?php if ($message): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="form-container">
        <form name="preJudicialForm" method="post" action="registro_prejudicial.php" enctype="multipart/form-data" onsubmit="return validarFormularioPreJudicial()">
            <div class="row">
                <div class="col-md-12 border p-3">
                    <h4>Información de la Etapa Pre-Judicial</h4>
                    <div class="mb-2">
                        <label class="fw-bold">Acto:</label>
                        <select name="acto" required class="form-control">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="Inicio caso prejudicial">Inicio caso prejudicial</option>
                            <option value="Notificación">Notificación</option>
                            <option value="Amortización">Amortización</option>
                            <option value="Cambio Gestor">Cambio Gestor</option>
                            <option value="Postergación">Postergación</option>
                            <option value="Fin de caso">Fin de caso</option>
                            <option value="Pasa a Judicial">Pasa a Judicial</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Número de Notificación/Voucher:</label>
                        <input type="text" name="n_de_notif_voucher" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Descripción:</label>
                        <textarea name="descripcion" required class="form-control"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Notificación/Compromiso de Pago:</label>
                        <input type="file" name="notif_compromiso_pago_evidencia"
                            accept=".docx, .pdf, .jpg, .png"
                            required
                            class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Fecha Clave:</label>
                        <input type="date" name="fecha_clave" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Acción en Fecha Clave:</label>
                        <input type="text" name="accion_fecha_clave" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Actor Involucrado:</label>
                        <select name="actor" required class="form-control">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="Gestor">Gestor</option>
                            <option value="Cliente">Cliente</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Administrador">Administrador</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Evidencia 1:</label>
                        <input type="file" name="evidencia1_localizacion" accept="image/*" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Evidencia 2:</label>
                        <input type="file" name="evidencia2_foto_fecha" accept="image/*" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Días de Mora:</label>
                        <input type="number" name="dias_de_mora" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Días de Mora Pre-Judicial:</label>
                        <input type="number" name="dias_mora_PJ" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Interés:</label>
                        <input type="number" step="0.01" name="interes" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Saldo más Interés:</label>
                        <input type="number" step="0.01" name="saldo_int" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Monto Amortizado:</label>
                        <input type="number" step="0.01" name="monto_amortizado" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Saldo a la Fecha:</label>
                        <input type="number" step="0.01" name="saldo_fecha" required class="form-control">
                    </div>
                </div>
            </div>
            <div class="fixed-buttons">
                <button type="submit" class="btn btn-primary mt-3">Registrar</button>
                <button type="reset" class="btn btn-secondary mt-3">Limpiar</button>
                <br>
                <button type="button" class="btn btn-danger mt-3" onclick="cerrarRegistroPreJudicial()">Salir</button>
            </div>
        </form>
    </div>
</body>
</html>