<?php
    session_start();
    ob_start();
    include 'conexion.php';

    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $action = $_POST['action'];

        if ($action == 'agregar') {
            $producto = $_POST['producto'];
            $cantidad = $_POST['cantidad'];
            $precio = $_POST['precio'];

            $stmt = $conn->prepare("INSERT INTO ventas (producto, cantidad, precio) VALUES (?, ?, ?)");
            $stmt->bind_param("sid", $producto, $cantidad, $precio);
            if ($stmt->execute()) {
                header("Location: ventas.php");
                exit();
            } else {
                echo "Error al agregar la venta: " . $conn->error;
            }
            $stmt->close();
        } elseif ($action == 'modificar') {
            $id = $_POST['id'];
            $producto = $_POST['producto'];
            $cantidad = $_POST['cantidad'];
            $precio = $_POST['precio'];

            $stmt = $conn->prepare("UPDATE ventas SET producto=?, cantidad=?, precio=? WHERE id=?");
            $stmt->bind_param("sidi", $producto, $cantidad, $precio, $id);

            if ($stmt->execute()) {
                header("Location: ventas.php");
                exit();
            } else {
                echo "Error al modificar la venta: " . $conn->error;
            }
            $stmt->close();
        } elseif ($action == 'eliminar') {
            $id = $_POST['id'];

            $stmt = $conn->prepare("DELETE FROM ventas WHERE id=?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                header("Location: ventas.php");
                exit();
            } else {
                echo "Error al eliminar la venta: " . $conn->error;
            }
            $stmt->close();
        }
    }
    $result = $conn->query("SELECT * FROM ventas");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link href="diseño.css" rel="stylesheet" type="text/css">
    <meta charset="utf-8">
    <title>Sistema de ventas de motos</title>
</head>
<body>
    <h3>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></h3><br>
    <a href="logout.php"><button class="logout">Cerrar Sesión</button></a>
    <div class="caja">
    <div class="contenedor">
        <form method="POST" action="ventas.php">
            <input type="hidden" name="action" value="agregar">
            <h3>producto</h3>
            <input type="text" name="producto" required class="campo">
            <h3>Cantidad</h3>
            <input type="number" name="cantidad" required class="campo">
            <h3>Precio</h3>
            <input type="number" step="0.01" name="precio" required class="campo">
            <br>
            <button type="submit" class="venta">Agregar venta</button>
        </form>
    </div>
    <img src="imagenes/moto.png" class="imagen">
</div>


    <table border="1" class="tabla">
        <tr class="fecha">
            <th>ID</th>
            <th>producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <form method="POST" action="ventas.php">
                    <td class="fecha"><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><input type="text" name="producto" value="<?php echo htmlspecialchars($row['producto']); ?>" required></td>
                    <td><input type="number" name="cantidad" value="<?php echo htmlspecialchars($row['cantidad']); ?>" required></td>
                    <td><input type="number" step="0.01" name="precio" value="<?php echo htmlspecialchars($row['precio']); ?>" required></td>
                    <td class="fecha"><?php echo htmlspecialchars($row['Fecha']); ?></td>
                    <td>
                      <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                        <button type="submit" name="action" value="modificar">Modificar</button><br>
                      <button type="submit" name="action" value="eliminar">Eliminar</button>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>