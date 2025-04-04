<?php
require "../conexion_db/connection.php";

$id_cliente = isset($_GET['id_cliente']) ? $_GET['id_cliente'] : '';
$cliente = [];
$monto_abonado = 0; // Inicializar con un valor predeterminado
$plazo_credito = 0; // Inicializar con un valor predeterminado

if ($id_cliente) {
    $sql = "SELECT nombre, apellidos, dni, monto, saldo, fecha_desembolso, fecha_vencimiento FROM clientes WHERE id_cliente='$id_cliente'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $cliente = $result->fetch_assoc();
        $monto_abonado = $cliente['monto'] - $cliente['saldo'];
        $fecha_desembolso = new DateTime($cliente['fecha_desembolso']);
        $fecha_vencimiento = new DateTime($cliente['fecha_vencimiento']);
        $plazo_credito = $fecha_vencimiento->diff($fecha_desembolso)->days;
    } else {
        die("Error: El ID del cliente no existe en la base de datos.");
    }
} else {
    die("Error: ID del cliente no proporcionado.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha_acto = date('Y-m-d H:i:s');

    // Obtener la fecha de inicio del caso (fecha_acto del ID 1) y la fecha_clave
    $sql_select_inicio = "SELECT fecha_acto, fecha_clave FROM etapa_prejudicial WHERE id_cliente = $id_cliente AND id = 1";
    $result_inicio = $conn->query($sql_select_inicio);

    if ($result_inicio->num_rows > 0) {
        $row_inicio = $result_inicio->fetch_assoc();
        $fecha_inicio_caso = $row_inicio['fecha_acto'];
        $fecha_clave_inicio = $row_inicio['fecha_clave'];
    } else {
        // Si no hay registros, fecha_inicio_caso y fecha_clave_inicio son la misma que fecha_acto
        $fecha_inicio_caso = $fecha_acto;
        $fecha_clave_inicio = $fecha_acto;
    }

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
    $saldo_int = $_POST["saldo_int"];
    $monto_amortizado = $_POST["monto_amortizado"];

    // Calcular dias_mora_PJ
    $dias_mora_PJ = calcularDiasMoraPJ($fecha_acto, $fecha_inicio_caso);
    // Calcular saldo_fecha
    $saldo_fecha = $saldo_int - $monto_amortizado;
    // Calcular dias_de_mora usando la fecha de vencimiento del cliente
    $dias_de_mora = calcularDiasDeMora($fecha_acto, $cliente['fecha_vencimiento']);
    // Asignar el valor de dias_mora_PJ a interes
    $interes = $dias_mora_PJ;

    // Directorio de destino para los archivos subidos
    $target_dir = "uploads/";
    // Crear el directorio si no existe
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    move_uploaded_file($_FILES["notif_compromiso_pago_evidencia"]["tmp_name"], $target_dir . $notif_compromiso_pago_evidencia);
    move_uploaded_file($_FILES["evidencia1_localizacion"]["tmp_name"], $target_dir . $evidencia1_localizacion);
    move_uploaded_file($_FILES["evidencia2_foto_fecha"]["tmp_name"], $target_dir . $evidencia2_foto_fecha);

    // Insertar el nuevo registro
    $sql_insert = "INSERT INTO etapa_prejudicial (id_cliente, fecha_acto, acto, n_de_notif_voucher, descripcion, notif_compromiso_pago_evidencia, fecha_clave, accion_fecha_clave, actor, evidencia1_localizacion, evidencia2_foto_fecha, dias_de_mora, dias_mora_PJ, interes, saldo_int, monto_amortizado, saldo_fecha)
    VALUES ('$id_cliente', '$fecha_acto', '$acto', '$n_de_notif_voucher', '$descripcion', '$target_dir$notif_compromiso_pago_evidencia', '$fecha_clave', '$accion_fecha_clave', '$actor', '$target_dir$evidencia1_localizacion', '$target_dir$evidencia2_foto_fecha', '$dias_de_mora', '$dias_mora_PJ', '$interes', '$saldo_int', '$monto_amortizado', '$saldo_fecha')";

    if ($conn->query($sql_insert) === TRUE) {
        $last_id = $conn->insert_id;
        actualizarFilaAnterior($conn, $last_id, $fecha_acto, $id_cliente);
        $message = "Registro exitoso";
    } else {
        $message = "Error: " . $sql_insert . "<br>" . $conn->error;
    }
}

//falta condicionar con id_cliernte y id
function calcularDiasMoraPJ($fecha_acto, $fecha_inicio_caso)
{
    $fecha_acto = new DateTime($fecha_acto);
    $fecha_inicio_caso = new DateTime($fecha_inicio_caso);
    $interval = $fecha_acto->diff($fecha_inicio_caso);
    return $interval->days;
}

//correcto
function calcularDiasDeMora($fecha_acto, $fecha_vencimiento)
{
    $fecha_acto = new DateTime($fecha_acto);
    $fecha_vencimiento = new DateTime($fecha_vencimiento);
    // Si la fecha_acto es menor o igual a fecha_vencimiento, devolver 0
    if ($fecha_acto <= $fecha_vencimiento) {
        return 0;
    }
    // Calcular la diferencia de días
    $interval = $fecha_acto->diff($fecha_vencimiento);
    return $interval->days;
}

//correcto
function actualizarFilaAnterior($conn, $last_id, $fecha_acto, $id_cliente)
{
    $sql_select = "SELECT id, fecha_clave, actor FROM etapa_prejudicial WHERE id_cliente = $id_cliente AND id < $last_id ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql_select);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_anterior = $row['id'];
        $fecha_clave_anterior = $row['fecha_clave'];
        $actor = $row['actor'];

        $dias_desde_fecha_clave = calcularDiasDesdeFechaClave($fecha_acto, $fecha_clave_anterior);

        if ($actor == "Gestor") {
            $objetivo_logrado = $dias_desde_fecha_clave <= 0 ? "SI" : "NO";
        } else {
            $objetivo_logrado = "";
        }

        $sql_update = "UPDATE etapa_prejudicial SET dias_desde_fecha_clave = $dias_desde_fecha_clave, objetivo_logrado = '$objetivo_logrado' WHERE id = $id_anterior";
        $conn->query($sql_update);
    }
}

// falta cumplir funcion
function calcularDiasDesdeFechaClave($fecha_acto_siguiente, $fecha_clave)
{
    $fecha_acto_siguiente = new DateTime($fecha_acto_siguiente);
    $fecha_clave = new DateTime($fecha_clave);
    $interval = $fecha_acto_siguiente->diff($fecha_clave);
    $dias = $interval->days;

    if ($fecha_clave > $fecha_acto_siguiente) {
        $dias = -$dias - 1;
    }

    return $dias;
}

$conn->close();
?>



<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="validacion_etapas.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .form-container {
            display: none;
        }

        .form-container.active {
            display: block;
        }
    </style>
</head>

<body class="container mt-3">
    <div>
        <h2>Información del Cliente</h2>
    </div>
    <!-- Información del Cliente -->
    <div class="form-container border p-3 mb-3 active">
        <div class="row mb-2">
            <div class="col-md-6">
                <label class="fw-bold">Nombres:</label>
                <input type="text" value="<?php echo htmlspecialchars(isset($cliente['nombre']) ? $cliente['nombre'] . ' ' . $cliente['apellidos'] : ''); ?>" class="form-control" readonly>
            </div>
            <div class="col-md-6">
                <label class="fw-bold">DNI:</label>
                <input type="text" value="<?php echo htmlspecialchars($cliente['dni'] ?? ''); ?>" class="form-control" readonly>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-4">
                <label class="fw-bold">Monto:</label>
                <input type="number" value="<?php echo htmlspecialchars($cliente['monto'] ?? ''); ?>" class="form-control" readonly>
            </div>
            <div class="col-md-4">
                <label class="fw-bold">Saldo:</label>
                <input type="number" value="<?php echo htmlspecialchars($cliente['saldo'] ?? ''); ?>" class="form-control" readonly>
            </div>
            <div class="col-md-4">
                <label class="fw-bold">Monto Abonado:</label>
                <input type="text" value="<?php echo htmlspecialchars($monto_abonado); ?>" class="form-control" readonly>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-4">
                <label class="fw-bold">Fecha Desembolso:</label>
                <input type="text" value="<?php echo htmlspecialchars($cliente['fecha_desembolso'] ?? ''); ?>" class="form-control" readonly>
            </div>
            <div class="col-md-4">
                <label class="fw-bold">Fecha Vencimiento:</label>
                <input type="text" value="<?php echo htmlspecialchars($cliente['fecha_vencimiento'] ?? ''); ?>" class="form-control" readonly>
            </div>
            <div class="col-md-4">
                <label class="fw-bold">Plazo de Crédito (días):</label>
                <input type="text" value="<?php echo htmlspecialchars($plazo_credito); ?>" class="form-control" readonly>
            </div>
        </div>
    </div>

    <!-- Información de la Etapa Pre-Judicial y Judicial -->
    <div class="row">
        <div>
            <h2>Formulario de Etapa Prejudicial y Judicial</h2>
        </div>
        <!-- Formulario de Etapa Pre-Judicial -->
        <div class="col-md-8 border p-3 active">
            <?php if ($message): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form id="preJudicialForm" method="post" enctype="multipart/form-data" onsubmit="return enviarFormulario()">
                <input type="hidden" name="id_cliente" value="<?php echo htmlspecialchars($id_cliente); ?>">
                <h4>Información de la Etapa Pre-Judicial</h4>
                <div class="row mb-2">
                    <div class="col-md-6">
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
                    <div class="col-md-6">
                        <label class="fw-bold">Número de Notificación/Voucher:</label>
                        <input type="text" name="n_de_notif_voucher" required class="form-control">
                    </div>
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
                <div class="row mb-2">
                    <div class="col-md-6">
                        <label class="fw-bold">Evidencia 1:</label>
                        <input type="file" name="evidencia1_localizacion" accept="image/*" required class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Evidencia 2:</label>
                        <input type="file" name="evidencia2_foto_fecha" accept="image/*" required class="form-control">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="fw-bold">Saldo mas interes:</label>
                    <input type="number" step="0.01" name="saldo_int" required class="form-control">
                </div>
                <div class="mb-2">
                    <label class="fw-bold">Monto Amortizado:</label>
                    <input type="number" step="0.01" name="monto_amortizado" required class="form-control">
                </div>
                <div class="fixed-buttons">
                    <button type="submit" class="btn btn-primary mt-3">Registrar</button>
                    <button type="reset" class="btn btn-secondary mt-3">Limpiar</button>
                    <br>
                    <button type="button" class="btn btn-success mt-3" onclick="window.location.href='../registro_cliente/index.php'">Regresar</button>
                </div>
            </form>
        </div>

        <!-- Botón para abrir el formulario judicial -->
        <div class="col-md-4 border p-3">
            <button type="button" class="btn btn-info w-100 mt-3" onclick="toggleJudicialForm()">Judicial</button>

            <!-- Formulario de Etapa Judicial -->
            <div id="judicialFormContainer" class="form-container">
                <form id="judicialForm" enctype="multipart/form-data" >
                    <input type="hidden" name="id_cliente" value="<?php echo htmlspecialchars($id_cliente); ?>">
                    <h4>Información de la Etapa Judicial</h4>
                    <div id="message"></div>
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
                    <div class="fixed-buttons">
                        <button type="submit" class="btn btn-primary mt-3">Registrar</button>
                        <button type="reset" class="btn btn-secondary mt-3">Limpiar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </div>

    <script>
        function enviarFormulario(event) {
            // Validar el formulario antes de evitar el envío tradicional
            if (!validarFormularioPreJudicial()) {
                return false; // Si la validación falla, no se envía el formulario
            }
            event.preventDefault(); // Evita el envío tradicional del formulario

            var formData = new FormData(document.getElementById('preJudicialForm'));

            fetch('registro_prejudicial.php', { // Asegúrate de que la URL sea correcta
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    // Maneja la respuesta del servidor
                    alert('Registro exitoso');
                    // Puedes actualizar parte de la página si es necesario
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un error al registrar los datos.');
                });

            return false; // Evita el comportamiento predeterminado del formulario
        }

        function toggleJudicialForm() {
            const judicialFormContainer = document.getElementById('judicialFormContainer');
            judicialFormContainer.classList.toggle('active');
        }

        document.getElementById('judicialForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Evita el envío tradicional del formulario
            var formData = new FormData(this);
            

            fetch('http://localhost/proyecto_financiera/judicial/registro_judicial.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    // Muestra el mensaje de éxito o error
                    document.getElementById('message').innerHTML = data;
                    // Limpia el formulario después de registrar
                    document.getElementById('judicialForm').reset();
                })
                .catch(error => {
                    console.error('Error:', error);
                });
                
        });
        
    </script>
</body>

</html>