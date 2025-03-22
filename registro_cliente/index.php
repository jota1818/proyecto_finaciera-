<!DOCTYPE html>
<html>
<head>
    <title>Búsqueda de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <script src="validacion.js" defer></script>
</head>
<body class="container-fluid mt-3">
    <div class="row">
        <!-- Barra lateral vertical -->
        <div class="col-md-4">
            <div class="vertical-button-container">
                <button type="button" class="btn btn-primary mb-3" onclick="cargarRegistro()">
                    Registrar nuevo cliente
                </button>
                <div id="registroCliente" class="form-container">
                    <div id="registroContent">
                        <!-- El formulario de registro se cargará aquí por defecto -->
                        <?php include 'registrar.php'; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido principal horizontal -->
        <div class="col-md-8">
            <!-- Lista de Clientes -->
            <div class="mb-4 p-3 border">
                <div class="fixed-header">
                    <h3>Lista de Clientes</h3>
                </div>
                <div class="scrollable-table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0)">Nombre &#9660;</th>
                                <th onclick="sortTable(1)">Apellidos &#9660;</th>
                                <th onclick="sortTable(2)">DNI &#9660;</th>
                                <th onclick="sortTable(3)">Teléfono &#9660;</th>
                                <th onclick="sortTable(4)">Monto &#9660;</th>
                                <th onclick="sortTable(5)">Saldo &#9660;</th>
                                <th onclick="sortTable(6)">Fecha Clave &#9660;</th>
                                <th onclick="sortTable(7)">Acción en Fecha Clave &#9660;</th>
                                <th class="fixed-column">Información</th>
                            </tr>
                        </thead>
                        <tbody id="listaClientes">
                            <!-- La lista de clientes se cargará aquí al inicio -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Búsqueda de Clientes -->
            <div class="mb-4 p-3 border">
                <h2>Búsqueda de Clientes</h2>
                <form id="busquedaForm" method="post" onsubmit="return buscarCliente(event);">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label class="fw-bold">DNI:</label>
                                <input type="number" id="dniInput" name="dni" class="form-control" oninput="buscarClientePorDNI()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label class="fw-bold">Nombre:</label>
                                <input type="text" name="nombre" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label class="fw-bold">Apellidos:</label>
                                <input type="text" name="apellidos" class="form-control">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Buscar</button>
                </form>
            </div>

            <!-- Cuadro blanco para la información del cliente -->
            <div id="clienteDetalles" class="p-3 border mt-4">
                <div class="fixed-header">
                    <h4>Información del Cliente</h4>
                </div>
                <div class="details-container" id="detallesContent">
                    <!-- Mensaje por defecto -->
                    <p>No se ha seleccionado ningún cliente.</p>
                </div>
                <div class="fixed-buttons mt-3">
                    <button type="button" class="btn btn-primary">Ver Historia</button>
                    <button type="button" class="btn btn-secondary">Agregar Historia</button>
                    <button type="button" class="btn btn-success">Regresar</button>
                    <button type="button" class="btn btn-danger">Salir</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function sortTable(n) {
            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.querySelector(".table");
            switching = true;
            dir = "asc";

            while (switching) {
                switching = false;
                rows = table.rows;

                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i + 1].getElementsByTagName("TD")[n];

                    if (dir == "asc") {
                        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    } else if (dir == "desc") {
                        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                }

                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount++;
                } else {
                    if (switchcount == 0 && dir == "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }
        }

        function buscarCliente(event) {
            event.preventDefault(); // Evita que el formulario se envíe de la manera tradicional
            var formData = new FormData(document.getElementById('busquedaForm'));

            fetch('buscar_clientes.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('listaClientes').innerHTML = data;
            });
        }

        function buscarClientePorDNI() {
            var dni = document.getElementById('dniInput').value;
            if (dni) {
                fetch('detalles_cliente.php?dni=' + encodeURIComponent(dni))
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('detallesContent').innerHTML = data;
                        document.getElementById('clienteDetalles').style.display = 'block';
                    });
            } else {
                document.getElementById('detallesContent').innerHTML = '<p>No se ha seleccionado ningún cliente.</p>';
                document.getElementById('clienteDetalles').style.display = 'block';
            }
        }

        function cargarRegistro() {
            document.getElementById('registroCliente').style.display = 'block';
            fetch('registrar.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('registroContent').innerHTML = data;
                });
        }

        function mostrarCliente(dni) {
            fetch('detalles_cliente.php?dni=' + encodeURIComponent(dni))
                .then(response => response.text())
                .then(data => {
                    document.getElementById('detallesContent').innerHTML = data;
                    document.getElementById('clienteDetalles').style.display = 'block';
                });
        }

        // Cargar la lista de clientes al inicio
        window.onload = function() {
            fetch('buscar_clientes.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('listaClientes').innerHTML = data;
                });
        };
    </script>
</body>
</html>