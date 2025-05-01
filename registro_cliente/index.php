<!DOCTYPE html>
<html>

<head>
    <title>Búsqueda de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <link href="reduced_styles.css" rel="stylesheet"> <!-- Nuevo archivo CSS -->
    <script src="validacion.js" defer></script>
</head>

<body class="container-fluid mt-2">
    <div class="row gx-2">
        <!-- Barra lateral vertical -->
        <div class="col-md-4">
            <div class="vertical-button-cont">
                <div class="vertical-button-container">
                    <button type="button" class="btn btn-primary boton-registro" onclick="cargarRegistro()">
                        REGISTRAR NUEVO CLIENTE
                    </button>
                    <div id="registroCliente" class="form-container">
                        <div id="registroContent">
                            <!-- El formulario de registro se cargará aquí por defecto -->
                            <?php include 'registrar.php'; ?>
                        </div>
                    </div>
                </div>
                <div class="informe-button-container">
                    <button type="button" class="btn btn-warning informe-button" onclick="window.location.href='../reports/reporte.php'">REPORTE</button>
                </div>
            </div>
        </div>

        <!-- Contenido principal horizontal -->
        <div class="col-md-8">
            <!-- Lista de Clientes -->
            <div class="fixed-header">
                <h5>LISTA DE CLIENTES</h5>
            </div>
            <div class="scrollable-table-container">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th onclick="sortTable(0)">Nombre &#128317;</th>
                            <th onclick="sortTable(1)">Apellidos &#128317;</th>
                            <th onclick="sortTable(2)">DNI &#128317;</th>
                            <th onclick="sortTable(3)">Teléfono &#128317;</th>
                            <th onclick="sortTable(4)">Monto &#128317;</th>
                            <th onclick="sortTable(5)">Saldo &#128317;</th>
                            <th onclick="sortTable(6)">Fecha Clave &#128317;</th>
                            <th onclick="sortTable(7)">Acción en Fecha Clave &#128317;</th>
                            <th class="fixed-column">Información</th>
                        </tr>
                    </thead>
                    <tbody id="listaClientes">
                        <!-- La lista de clientes se cargará aquí al inicio -->
                    </tbody>
                </table>
            </div>

            <!-- Búsqueda de Clientes -->
            <div class="mb-2 border busqueda-clientes">
                <h5 class="client-search-title">BUSQUEDA DE CLIENTES</h5>
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
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </form>
            </div>

            <!-- Cuadro blanco para la información del cliente -->
            <div id="clienteDetalles" class="border">
                <div class="fixed-header">
                    <h5>INFORMACION DEL CLIENTE</h5>
                </div>
                <div class="details-container" id="detallesContent">
                    <!-- Mensaje por defecto -->
                    <p>No se ha seleccionado ningún cliente.</p>
                </div>
                <div class="fixed-buttons mt-3">
                    <!-- <button type="button" class="btn btn-primary">Ver Historia</button> -->
                    <button type="button" class="btn btn-primary" onclick="verHistorial()">Ver Historia</button>
                    <button type="button" class="btn btn-secondary" onclick="agregarHistoria()">Agregar Historia</button>
                    <button type="button" class="btn btn-success" onclick="window.location.href='index.php'">Regresar</button>

                    <button type="button" class="btn btn-danger">Salir</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Campo oculto para almacenar el DNI seleccionado -->
    <input type="hidden" id="selectedDni">
    <script>
        function agregarHistoria() {
            var dni = document.getElementById('selectedDni').value;
            console.log("DNI seleccionado:", dni); // Verifica si el DNI se está obteniendo correctamente

            if (dni) {
                fetch('../conexion_db/obtener_id_cliente.php?dni=' + encodeURIComponent(dni))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("Respuesta del servidor:", data); // Verifica la respuesta del servidor
                        if (data.id_cliente) {
                            window.location.href = '/proyecto_financiera/pre_judicial/registro_prejudicial.php?id_cliente=' + encodeURIComponent(data.id_cliente);
                        } else {
                            alert("No se pudo obtener el ID del cliente.");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("Hubo un error al obtener el ID del cliente.");
                    });
            } else {
                alert("Por favor, seleccione un cliente primero.");
            }
        }

        function verHistorial() {
            var dni = document.getElementById('selectedDni').value;
            if (dni) {
                fetch('../conexion_db/obtener_id_cliente.php?dni=' + encodeURIComponent(dni))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.id_cliente) {
                            window.location.href = '/proyecto_financiera/historial/record.php?id_cliente=' + encodeURIComponent(data.id_cliente);
                        } else {
                            alert("No se pudo obtener el ID del cliente.");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("Hubo un error al obtener el ID del cliente.");
                    });
            } else {
                alert("Por favor, seleccione un cliente primero.");
            }
        }


        function sortTable(n) {
            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.querySelector(".table");
            switching = true;
            // Determinar la dirección inicial
            dir = table.querySelectorAll("th")[n].getAttribute("data-order") === "asc" ? "asc" : "desc";

            while (switching) {
                switching = false;
                rows = table.rows;

                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i + 1].getElementsByTagName("TD")[n];

                    // Convertir a números si es la columna de Monto o Saldo
                    if (n === 4 || n === 5) {
                        xValue = parseFloat(x.innerHTML.replace(/,/g, ''));
                        yValue = parseFloat(y.innerHTML.replace(/,/g, ''));
                    } else {
                        xValue = x.innerHTML.toLowerCase();
                        yValue = y.innerHTML.toLowerCase();
                    }

                    if (dir == "asc") {
                        if (xValue > yValue) {
                            shouldSwitch = true;
                            break;
                        }
                    } else if (dir == "desc") {
                        if (xValue < yValue) {
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
                        table.querySelectorAll("th")[n].setAttribute("data-order", "desc");
                    } else {
                        dir = "asc";
                        table.querySelectorAll("th")[n].setAttribute("data-order", "asc");
                    }
                }
            }

        }

        function formatNumber(number) {
            return number.toLocaleString('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function cargarDatosClientes(data) {
            const listaClientes = document.getElementById('listaClientes');
            listaClientes.innerHTML = data.map(cliente => `
        <tr>
            <td>${cliente.nombre}</td>
            <td>${cliente.apellidos}</td>
            <td>${cliente.dni}</td>
            <td>${cliente.telefono}</td>
            <td>${formatNumber(parseFloat(cliente.monto))}</td>
            <td>${formatNumber(parseFloat(cliente.saldo))}</td>
            <td>${cliente.fecha_clave}</td>
            <td>${cliente.accion_fecha_clave}</td>
            <td><button onclick="mostrarCliente('${cliente.dni}')">Seleccionar</button></td>
        </tr>
            `).join('');
        }

        // Llamar a cargarDatosClientes con los datos obtenidos
        fetch('buscar_clientes.php')
            .then(response => response.json())
            .then(data => cargarDatosClientes(data));


        function buscarCliente(event) {
            event.preventDefault(); // Evita que el formulario se envíe de la manera tradicional
            var formData = new FormData(document.getElementById('busquedaForm'));

            fetch('buscar_clientes.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json()) // Asegúrate de que la respuesta se convierta a JSON
                .then(data => cargarDatosClientes(data));
        }

        function buscarClientePorDNI() {
            var dni = document.getElementById('dniInput').value;
            if (dni) {
                fetch('detalles_cliente.php?dni=' + encodeURIComponent(dni))
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('detallesContent').innerHTML = data;
                        document.getElementById('clienteDetalles').style.display = 'block';
                        // Almacenar el DNI en un campo oculto
                        document.getElementById('selectedDni').value = dni;
                        // Mostrar los detalles del cliente automáticamente
                        mostrarCliente(dni);
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

                    // Almacenar el DNI en un campo oculto
                    document.getElementById('selectedDni').value = dni;
                });
        }


        // Cargar la lista de clientes al inicio
        window.onload = function() {
            fetch('buscar_clientes.php')
                .then(response => response.json()) // Asegúrate de que la respuesta se convierta a JSON
                .then(data => cargarDatosClientes(data));
        };

        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Los meses son base 0
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }

        function cargarDatosClientes(data) {
            const listaClientes = document.getElementById('listaClientes');
            listaClientes.innerHTML = data.map(cliente => `
        <tr>
            <td>${cliente.nombre}</td>
            <td>${cliente.apellidos}</td>
            <td>${cliente.dni}</td>
            <td>${cliente.telefono}</td>
            <td>${formatNumber(parseFloat(cliente.monto))}</td>
            <td>${formatNumber(parseFloat(cliente.saldo))}</td>
            <td>${formatDate(cliente.fecha_clave)}</td>
            <td>${cliente.accion_fecha_clave}</td>
            <td><button onclick="mostrarCliente('${cliente.dni}')">Seleccionar</button></td>
        </tr>
        `).join('');
        }
    </script>
</body>

</html>