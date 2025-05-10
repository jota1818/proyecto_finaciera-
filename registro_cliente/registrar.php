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
            flex: 1; /* Ocupa el espacio restante en el contenedor vertical */
            height: 517px;
            /* Altura fija para el contenedor del formulario */
            overflow-y: auto;
            /* Solo scroll vertical */
            overflow-x: hidden;
            /* Oculta scroll horizontal */
            /* Habilitar desplazamiento vertical */
            padding-right: 2px;
            /* Espacio para la barra de desplazamiento */
            background-color: #E0E0E0;
        }

        .fixed-buttonss {
            background-color: #E0E0E0 !important;
            position: absolute;
            /* Cambiar a absolute */
            position: sticky;
            bottom: 0;
            background-color: white;
            padding: 5px;
            justify-content: center;
            width: 100%;
            /* Centra los botones horizontalmente */
            gap: 10px;
            /* Espacio entre los botones */
            padding: 0;
            justify-content: space-around;
            /* Centra los botones horizontalmente */
            margin-top: 0px;
            /* Añade espacio entre los botones y el contenido */
            display: flex;
            justify-content: space-between;
            /* Distribuye los botones uniformemente */
        }

        .fixed-buttonss button {
            padding: 3px;
            font-size: 10px;
            /* Ajusta el tamaño de la fuente si es necesario */
            border: none;
            cursor: pointer;
            background-color: #0056b3;
            transition: background-color 0.3s;
            /* Transición suave */
            font-weight: bold;
            /* Texto en negrita */
            flex: 1;
            /* Cada botón ocupa el mismo espacio */
        }

        .fixed-buttonss button:hover {
            font-weight: bold;
            /* Texto en negrita */
            background-color: #000000;
            /* Color azul al pasar el mouse */
            color: #ffffff;
            /* Cambia el color del texto si lo deseas */
        }

        /* Estilo para los campos de entrada */
        .form-control {
            height: 20px;
            /* Altura fija para los campos de entrada */
            width: 100%;
            /* Ancho completo */
            box-sizing: border-box;
            /* Incluye el padding y el borde en el ancho y alto */
            padding: 0;
            /* Elimina el padding interno */
            margin: 0;
            /* Elimina el margen externo */
            border-radius: 0;
            /* Elimina los bordes redondeados */
            border: 1px solid #ccc;
            /* Añade un borde cuadrado */
        }

        .titulo-seccion {
            background-color: #003366;
            /* Puedes cambiarlo al color que desees */
            border-radius: 0px;
            padding: 0px;
            margin-bottom: 0px;
            color: #ffffff;
        }

        .titulo-seccion h5 {
            margin: 0;
            padding: 3px;
            /* Puedes poner 0 si no quieres nada de espacio interno */
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body class="container mt-3">
    <div class="form-container">
        <form name="registroForm" method="post" action="registrar.php" onsubmit="return validarFormulario()">
            <div class="row">
                <div class="col-md-12">
                    <div class="titulo-seccion">
                        <h5>Información del Cliente</h5>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="fw-bold">Nombre</label>
                            <input type="text" name="nombre" required class="form-control" placeholder="Nombre">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Apellidos</label>
                            <input type="text" name="apellidos" required class="form-control" placeholder="Apellidos">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="fw-bold">DNI</label>
                            <input type="text" name="dni" required class="form-control" placeholder="DNI" onblur="verificarDNI(this.value)">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Teléfono</label>
                            <input type="text" name="telefono" required class="form-control" placeholder="Teléfono">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Fecha Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" required class="form-control">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Ocupación</label>
                        <input type="text" name="ocupacion" required class="form-control" placeholder="Ocupación y Referencias">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Domicilio 1</label>
                        <input type="text" name="domicilio1" required class="form-control" placeholder="Domicilio 1">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Referencia 1</label>
                        <input type="text" name="referencia1" required class="form-control" placeholder="Referencia 1">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Domicilio 2</label>
                        <input type="text" name="domicilio2" class="form-control" placeholder="Domicilio 2" placeholder="Domicilio 2">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Referencia 2</label>
                        <input type="text" name="referencia2" class="form-control" placeholder="Referencia 2">
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label class="fw-bold">Agencia</label>
                            <select name="agencia" required class="form-control">
                                <option value="" disabled selected>Seleccione una opción</option>
                                <option value="Ayacucho">Ayacucho</option>
                                <option value="Huancayo">Huancayo</option>
                                <option value="Lima">Lima</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">Tipo Crédito</label>
                            <select name="tipo_credito" required class="form-control">
                                <option value="" disabled selected>Seleccione una opción</option>
                                <option value="Diario">Diario</option>
                                <option value="Semanal">Semanal</option>
                                <option value="Mensual">Mensual</option>
                                <option value="Anual">Anual</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">Clas. Riesgo</label>
                            <select name="clasificacion_riesgo" required class="form-control">
                                <option value="" disabled selected>Seleccione una opción</option>
                                <option value="NOR">NOR</option>
                                <option value="CPP">CPP</option>
                                <option value="DEF">DEF</option>
                                <option value="PER">PER</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">Estado</label>
                            <input type="text" name="estado" required class="form-control" placeholder="Estado">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label class="fw-bold">Monto Créd.</label>
                            <input type="number" step="0.01" name="monto" required class="form-control" placeholder="Monto Créd.">
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">Fecha Desemb.</label>
                            <input type="date" name="fecha_desembolso" required class="form-control" placeholder="Fecha Desemb.">
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">Saldo Créd.</label>
                            <input type="number" step="0.01" name="saldo" required class="form-control" placeholder="Saldo Créd.">
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">Fec. Vencim.</label>
                            <input type="date" name="fecha_vencimiento" required class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="titulo-seccion">
                        <h5>Información del Aval</h5>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="fw-bold">Nombre</label>
                            <input type="text" name="nombre_garante" class="form-control" placeholder="Nombre">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Apellidos</label>
                            <input type="text" name="apellidos_garante" class="form-control" placeholder="Apellidos">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="fw-bold">DNI</label>
                            <input type="text" name="dni_garante" class="form-control" placeholder="DNI">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Teléfono</label>
                            <input type="number" name="telefono_garante" class="form-control" placeholder="Teléfono">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Fecha Nacimiento</label>
                            <input type="date" name="fecha_nacimiento_garante" class="form-control">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Ocupación</label>
                        <input type="text" name="ocupacion_garante" class="form-control" placeholder="Ocupación">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Domicilio 1</label>
                        <input type="text" name="domicilio1_garante" class="form-control" placeholder="Domicilio 1">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Referencia 1</label>
                        <input type="text" name="referencia1_garante" class="form-control" placeholder="Referencia 1">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Domicilio 2</label>
                        <input type="text" name="domicilio2_garante" class="form-control" placeholder="Domicilio 2">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Referencia 2</label>
                        <input type="text" name="referencia2_garante" class="form-control" placeholder="Referencia 2">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Clasificación de Riesgo</label>
                        <select name="clasificacion_riesgo_garante" class="form-control">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="NOR">NOR</option>
                            <option value="CPP">CPP</option>
                            <option value="DEF">DEF</option>
                            <option value="PER">PER</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="titulo-seccion">
                        <h5>Programación de Acciones y Responsabilidades</h5>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="fw-bold">Fecha clave</label>
                            <input type="date" name="fecha_clave" required class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label class="fw-bold">Actividad programada</label>
                            <input type="text" name="accion_fecha_clave" required class="form-control" placeholder="Actividad programada">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="fw-bold">Gestor</label>
                            <input type="number" name="gestor" required class="form-control" min="1" placeholder="Gestor">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Supervisor</label>
                            <input type="number" name="supervisor" required class="form-control" min="1" placeholder="Supervisor">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Administrador</label>
                            <input type="number" name="administrador" required class="form-control" min="1" placeholder="Administrador">
                        </div>
                    </div>
                </div>
            </div>
            <div class="fixed-buttonss">
                <button type="submit" class="btn btn-primary mt-3">Registrar</button>
                <button type="reset" class="btn btn-secondary mt-3">Limpiar</button>
            </div>
        </form>
    </div>
</body>

</html>