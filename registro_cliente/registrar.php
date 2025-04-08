<?php
require "../conexion_db/connection.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // datos cliente
    $dni = $_POST["dni"];
    $nombre = $_POST["nombre"];
    $apellidos = $_POST["apellidos"];
    $telefono = $_POST["telefono"];
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $domicilio1 = $_POST["domicilio1"];
    $referencia1 = $_POST["referencia1"];
    $domicilio2 = isset($_POST["domicilio2"]) ? $_POST["domicilio2"] : '';
    $referencia2 = isset($_POST["referencia2"]) ? $_POST["referencia2"] : '';
    $ocupacion = $_POST["ocupacion"];
    $clasificacion_riesgo = $_POST["clasificacion_riesgo"];
    $agencia = $_POST["agencia"]; //nuevo
    $tipo_credito = $_POST["tipo_credito"]; //nuevo
    $estado = $_POST["estado"]; //nuevo
    $fecha_desembolso = $_POST["fecha_desembolso"];
    $fecha_vencimiento = $_POST["fecha_vencimiento"];
    $monto = $_POST["monto"];
    $saldo = $_POST["saldo"];
    // datos garante
    $nombre_garante = $_POST["nombre_garante"] ? $_POST["nombre_garante"] : '';
    $apellidos_garante = $_POST["apellidos_garante"] ? $_POST["apellidos_garante"] : '';
    $dni_garante = $_POST["dni_garante"] ? $_POST["dni_garante"] : '';
    $telefono_garante = $_POST["telefono_garante"] ? $_POST["telefono_garante"] : '';
    $fecha_nacimiento_garante = $_POST["fecha_nacimiento_garante"] ? $_POST["fecha_nacimiento_garante"] : '';
    $domicilio1_garante = $_POST["domicilio1_garante"] ? $_POST["domicilio1_garante"] : '';
    $referencia1_garante = $_POST["referencia1_garante"] ? $_POST["referencia1_garante"] : '';
    $domicilio2_garante = isset($_POST["domicilio2_garante"]) ? $_POST["domicilio2_garante"] : '';
    $referencia2_garante = isset($_POST["referencia2_garante"]) ? $_POST["referencia2_garante"] : '';
    $ocupacion_garante = $_POST["ocupacion_garante"] ? $_POST["ocupacion_garante"] : ''; 
    $clasificacion_riesgo_garante = $_POST["clasificacion_riesgo_garante"] ? $_POST["clasificacion_riesgo_garante"] : '';
    //dato Fecha Programada
    $fecha_clave = $_POST["fecha_clave"];
    $accion_fecha_clave = $_POST["accion_fecha_clave"];
    // dato personal asignado
    $gestor = $_POST["gestor"];
    $supervisor = $_POST["supervisor"];
    $administrador = $_POST["administrador"];

    $sql = "INSERT INTO clientes (nombre, apellidos, dni, telefono, fecha_nacimiento, domicilio1, referencia1, domicilio2, referencia2, ocupacion, clasificacion_riesgo, agencia, tipo_credito, estado, fecha_desembolso, fecha_vencimiento, monto, saldo, nombre_garante, apellidos_garante, dni_garante, telefono_garante, fecha_nacimiento_garante, domicilio1_garante, referencia1_garante, domicilio2_garante, referencia2_garante, ocupacion_garante, clasificacion_riesgo_garante, fecha_clave, accion_fecha_clave, gestor, supervisor, administrador)
    VALUES ('$nombre', '$apellidos', '$dni', '$telefono', '$fecha_nacimiento', '$domicilio1', '$referencia1', '$domicilio2', '$referencia2', '$ocupacion', '$clasificacion_riesgo', '$agencia', '$tipo_credito', '$estado', '$fecha_desembolso', '$fecha_vencimiento', '$monto', '$saldo', '$nombre_garante', '$apellidos_garante', '$dni_garante', '$telefono_garante', '$fecha_nacimiento_garante', '$domicilio1_garante', '$referencia1_garante', '$domicilio2_garante', '$referencia2_garante', '$ocupacion_garante', '$clasificacion_riesgo_garante', '$fecha_clave', '$accion_fecha_clave', '$gestor', '$supervisor', '$administrador')";

    if ($conn->query($sql) === TRUE) {
        $message = "Registro exitoso";
        header("Location: index.php?message=" . urlencode($message));
        exit();
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        .form-container {
            height: 1000px; /* Altura fija para el contenedor del formulario */
            overflow-y: auto; /* Habilitar desplazamiento vertical */
            padding-right: 15px; /* Espacio para la barra de desplazamiento */
        }
        .fixed-buttonss {
            position: sticky;
            bottom: 0;
            background-color: white;
            padding: 10px;
            border-top: 1px solid #ddd;
            justify-content: center;  /* Centra los botones horizontalmente */
            gap: 100px; /* Espacio entre los botones */
        }
        .fixed-buttonss button {
            width: 200px; /* Ajusta el ancho de los botones */
            font-size: 16px; /* Ajusta el tamaño de la fuente si es necesario */
        }
    </style>
</head>
<body class="container mt-3">
    <div>
        <h2>Formulario de Registro</h2>
    </div>
    <div class="form-container">
        <form name="registroForm" method="post" action="registrar.php" onsubmit="return validarFormulario()">
            <div class="row">
                <div class="col-md-12 border p-3">
                    <h4>Información del Cliente</h4>
                    <div class="mb-2">
                        <label class="fw-bold">Nombre:</label>
                        <input type="text" name="nombre" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Apellidos:</label>
                        <input type="text" name="apellidos" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">DNI:</label>
                        <input type="text" name="dni" required class="form-control" onblur="verificarDNI(this.value)">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Teléfono:</label>
                        <input type="text" name="telefono" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Fecha de Nacimiento:</label>
                        <input type="date" name="fecha_nacimiento" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Domicilio 1:</label>
                        <input type="text" name="domicilio1" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Referencia 1:</label>
                        <input type="text" name="referencia1" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Domicilio 2:</label>
                        <input type="text" name="domicilio2" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Referencia 2:</label>
                        <input type="text" name="referencia2" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Ocupación:</label>
                        <input type="text" name="ocupacion" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Clasificación de Riesgo:</label>
                        <select name="clasificacion_riesgo" required class="form-control">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="NOR">NOR</option>
                            <option value="CPP">CPP</option>
                            <option value="DEF">DEF</option>
                            <option value="PER">PER</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Agencia:</label>
                        <input type="text" name="agencia" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Tipo de credito:</label>
                        <input type="text" name="tipo_credito" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Estado:</label>
                        <input type="text" name="estado" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Fecha de Desembolso:</label>
                        <input type="date" name="fecha_desembolso" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Fecha de Vencimiento:</label>
                        <input type="date" name="fecha_vencimiento" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Monto:</label>
                        <input type="number" step="0.01" name="monto" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Saldo:</label>
                        <input type="number" step="0.01" name="saldo" required class="form-control">
                    </div>
                </div>
                <div class="col-md-12 border p-3">
                    <h4>Información del Garante</h4>
                    <div class="mb-2">
                        <label class="fw-bold">Nombre:</label>
                        <input type="text" name="nombre_garante"  class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Apellidos:</label>
                        <input type="text" name="apellidos_garante"  class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">DNI:</label>
                        <input type="text" name="dni_garante"  class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Teléfono:</label>
                        <input type="number" name="telefono_garante"  class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Fecha de Nacimiento:</label>
                        <input type="date" name="fecha_nacimiento_garante"  class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Domicilio 1:</label>
                        <input type="text" name="domicilio1_garante"  class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Referencia 1:</label>
                        <input type="text" name="referencia1_garante"  class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Domicilio 2:</label>
                        <input type="text" name="domicilio2_garante" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Referencia 2:</label>
                        <input type="text" name="referencia2_garante" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Ocupación:</label>
                        <input type="text" name="ocupacion_garante"  class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Clasificación de Riesgo:</label>
                        <select name="clasificacion_riesgo_garante"  class="form-control">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="NOR">NOR</option>
                            <option value="CPP">CPP</option>
                            <option value="DEF">DEF</option>
                            <option value="PER">PER</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 border p-3">
                    <h4>Fecha Programada</h4>
                    <div class="mb-2">
                        <label class="fw-bold">Fecha clave:</label>
                        <input type="date" name="fecha_clave" required class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Acción en fecha clave:</label>
                        <input type="text" name="accion_fecha_clave" required class="form-control">
                    </div>
                </div>
                <div class="col-md-12 border p-3">
                    <h4>Personal Asignado</h4>
                    <div class="mb-2">
                        <label class="fw-bold">Gestor:</label>
                        <input type="number" name="gestor" required class="form-control" min="1" max="9">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Supervisor:</label>
                        <input type="number" name="supervisor" required class="form-control" min="1" max="9">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Administrador:</label>
                        <input type="number" name="administrador" required class="form-control" min="1" max="9">
                    </div>
                </div>
            </div>
            <div class="fixed-buttonss">
                <button type="submit" class="btn btn-primary mt-3">Registrar</button>
                <button type="reset" class="btn btn-secondary mt-3">Limpiar</button>
                <br>
                <button type="button" class="btn btn-danger mt-3" onclick="cerrarRegistro()">Salir</button>
            </div>
        </form>
    </div>
</body>
</html>