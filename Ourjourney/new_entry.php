<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $is_special = isset($_POST['is_special']) ? 1 : 0;
    $image = null;

    if ($_FILES['image']['name']) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image = $_FILES["image"]["name"];
    }

    $stmt = $conn->prepare("INSERT INTO entries (user_id, title, content, image, is_special) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $user_id, $title, $content, $image, $is_special);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Entrada</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nueva Entrada</h1>
        </div>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="content">Contenido</label>
                <textarea id="content" name="content" required></textarea>
            </div>
            <div class="form-group">
                <label for="image">Imagen</label>
                <input type="file" id="image" name="image">
            </div>
            <div class="form-group">
                <label for="is_special">
                    <input type="checkbox" id="is_special" name="is_special"> Marcar como suceso especial
                </label>
            </div>
            <button type="submit" class="button">Guardar</button>
        </form>
    </div>
</body>
</html>
