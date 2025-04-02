<?php
require "../conexion_db/connection.php";

$id_cliente = isset($_GET['id_cliente']) ? $_GET['id_cliente'] : '';
$historial = [];

if ($id_cliente) {
    $sql = "SELECT * FROM etapa_prejudicial WHERE id_cliente='$id_cliente'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $historial = $result->fetch_all(MYSQLI_ASSOC);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-3">
    <h2>Historial del Cliente</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Fecha Acto</th>
                <th>Acto</th>
                <th>Descripción</th>
                <th>Fecha Clave</th>
                <th>Acción en Fecha Clave</th>
                <th>Actor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($historial as $registro): ?>
                <tr>
                    <td><?php echo htmlspecialchars($registro['fecha_acto']); ?></td>
                    <td><?php echo htmlspecialchars($registro['acto']); ?></td>
                    <td><?php echo htmlspecialchars($registro['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($registro['fecha_clave']); ?></td>
                    <td><?php echo htmlspecialchars($registro['accion_fecha_clave']); ?></td>
                    <td><?php echo htmlspecialchars($registro['actor']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
