<?php
// redireccionar.php

// 1. Verificar si llegó un código a través del .htaccess
if (empty($_GET['codigo'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';
$codigo = trim($_GET['codigo']);

try {
    // 2. BUSCAR EL CÓDIGO EN LA BASE DE DATOS
    $stmt = $pdo->prepare("SELECT id, url_original FROM enlaces WHERE codigo_corto = :codigo LIMIT 1");
    $stmt->execute([':codigo' => $codigo]);
    $enlace = $stmt->fetch();

    if ($enlace) {
        // 3. ¡ENCONTRADO! SUMAR UN CLIC (Analítica profesional)
        // Hacemos un UPDATE usando el ID para que sea ultra rápido
        $update = $pdo->prepare("UPDATE enlaces SET clicks = clicks + 1 WHERE id = :id");
        $update->execute([':id' => $enlace['id']]);

        /* 4. REDIRECCIÓN CON ESTÁNDAR HTTP 301 (Nivel Avanzado)
           El código 301 le dice a los navegadores y a Google que esta es 
           una redirección permanente, lo cual es la práctica correcta en la industria.
        */
        header("Location: " . $enlace['url_original'], true, 301);
        exit;

    } else {
        // Si el código no existe en la base de datos, lo mandamos al index o a un error 404
        die("<h1>Error 404: El enlace corto no existe o ha expirado.</h1><a href='index.php'>Volver al inicio</a>");
    }

} catch (PDOException $e) {
    die("Error crítico en el servidor: " . $e->getMessage());
}
