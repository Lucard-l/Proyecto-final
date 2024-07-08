?php
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
            <h1>Bienvenido a tu Diario</h1>
            <a href="new_entry.php" class="button">Nueva Entrada</a>
            <a href="logout.php" class="button">Cerrar Sesión</a>
        </div>
        
        <!-- Sección de notificaciones -->
        <div class="notifications">
            <?php
            $stmt = $conn->prepare("SELECT id, title, created_at FROM entries WHERE user_id = ? AND is_special = 1");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $today = date("Y-m-d");
            while ($row = $result->fetch_assoc()) {
                $entry_date = date("Y-m-d", strtotime($row['created_at']));
                $anniversary = date("Y-m-d", strtotime($entry_date . ' + 1 year'));

                if ($anniversary == $today) {
                    echo "<div class='notification'>";
                    echo "<p>¡Hoy es el aniversario de un suceso especial: <strong>" . htmlspecialchars($row['title']) . "</strong>!</p>";
                    echo "</div>";
                }
            }
            ?>
        </div>
        
        <!-- Sección de entradas -->
        <div class="entries">
            <?php
            $stmt = $conn->prepare("SELECT id, title, content, image, is_special, created_at FROM entries WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                echo "<div class='entry'>";
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
</body>
</html>
