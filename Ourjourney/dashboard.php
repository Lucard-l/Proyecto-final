<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="fadeIn">Bienvenido a Our Journey</h1>
            <a href="new_entry.php" class="button fadeIn">Nueva Entrada</a>
            <a href="logout.php" class="button fadeIn">Cerrar Sesión</a>
        </div>
        
        <!-- Sección de notificaciones -->
        <div class="notifications">
            <?php
            // Consulta para obtener las entradas especiales cuyo aniversario ya ha pasado
            $stmt = $conn->prepare("SELECT id, title, created_at FROM entries WHERE user_id = ? AND is_special = 1 AND DATE(created_at) < CURDATE() ORDER BY created_at DESC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                // Calculamos la fecha de aniversario para esta entrada especial
                $entry_date = date("m-d", strtotime($row['created_at']));
                $today = date("m-d");

                if ($entry_date == $today) {
                    echo "<div class='notification fadeIn'>";
                    echo "<p>¡Hoy es el aniversario de un suceso especial pasado: <strong>" . htmlspecialchars($row['title']) . "</strong>!</p>";
                    echo "</div>";
                }
            }
            ?>
        </div>
        
        <!-- Sección de entradas -->
        <div class="entries">
            <?php
            // Consulta para obtener todas las entradas del usuario ordenadas por fecha de creación
            $stmt = $conn->prepare("SELECT id, title, content, image, is_special, created_at FROM entries WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                echo "<div class='entry fadeIn'>";
                echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
                if ($row['is_special']) {
                    echo "<p class='special'>* Suceso Especial *</p>";
                }
                echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
                if ($row['image']) {
                    echo "<img src='images/" . htmlspecialchars($row['image']) . "' alt='Imagen'>";
                }
                echo "<p><small>Fecha: " . $row['created_at'] . "</small></p>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
    <script src="js/scripts.js"></script>
</body>
</html>