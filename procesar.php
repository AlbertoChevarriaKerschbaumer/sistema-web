<?php
// procesar.php

// 1. Validar que los datos lleguen por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['url_larga'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';

// 2. Limpiar y validar la URL ingresada
$url_original = trim($_POST['url_larga']);

// Buenas prácticas: Validar si realmente es una URL con formato correcto
if (!filter_var($url_original, FILTER_VALIDATE_URL)) {
    die("Error: El enlace ingresado no es una URL válida. Asegúrate de incluir http:// o https://");
}


// 3. Función para generar el código aleatorio (Ingeniería Inversa)
function generarCodigo($longitud = 6) {
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $total = strlen($caracteres);
    $codigo = '';
    for ($i = 0; $i < $longitud; $i++) {
        $codigo .= $caracteres[random_int(0, $total - 1)];
    }
    return $codigo;
}


// 4. BUCLE DE SEGURIDAD (Anti-duplicados)
$codigo_final = '';
$codigo_repetido = true;

while ($codigo_repetido) {
    $codigo_final = generarCodigo(6); // Genera uno temporal, ej: "7xK9pW"
    
    // Consultamos si ya existe en MySQL
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM enlaces WHERE codigo_corto = :codigo");
    $stmt->execute([':codigo' => $codigo_final]);
    
    // Si el conteo es 0, el código está libre. Rompemos el bucle.
    if ($stmt->fetchColumn() == 0) {
        $codigo_repetido = false;
    }
}


// 5. INSERTAR EN LA BASE DE DATOS
try {
    $insert = $pdo->prepare("INSERT INTO enlaces (url_original, codigo_corto) VALUES (:url, :codigo)");
    $insert->execute([
        ':url'    => $url_original,
        ':codigo' => $codigo_final
    ]);

    // 6. REDIRECCIÓN DE ÉXITO
    // Mandamos el código por la URL para que index.php lo pinte en la caja azul
    header("Location: index.php?resultado=" . $codigo_final);
    exit;

} catch (PDOException $e) {
    die("Error al guardar en la base de datos: " . $e->getMessage());
}