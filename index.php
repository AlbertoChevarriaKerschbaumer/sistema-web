<?php
// index.php (Al inicio del archivo)
require_once 'config/conexion.php';

try {
    // Traemos los últimos 5 enlaces generados
    $stmt = $pdo->query("SELECT url_original, codigo_corto, clicks FROM enlaces ORDER BY id DESC LIMIT 5");
    $ultimos_enlaces = $stmt->fetchAll();
} catch (PDOException $e) {
    $ultimos_enlaces = []; // Si falla, lo dejamos vacío para que no rompa el diseño
}
?>
<!DOCTYPE html lang="es">
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acortador de Enlaces Pro</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>

<div class="container">
    <div class="card">
        <h1>Acortador de URLs</h1>
        <p>Pega tu enlace largo y genera un link corto y fácil de compartir al instante.</p>

        <form action="procesar.php" method="POST">
            <div class="form-group">
                <input type="url" name="url_larga" placeholder="https://ejemplo.com/tu-enlace-gigante" required autocomplete="off">
                <button type="submit">Acortar</button>
            </div>
        </form>

        <?php if (isset($_GET['resultado'])): ?>
            <div class="result-box">
                <div>
                    <span style="color: var(--text-muted); font-size: 0.85rem; display:block; text-align:left;">Tu enlace corto:</span>
                    <a href="http://localhost/acortador/<?php echo htmlspecialchars($_GET['resultado']); ?>" target="_blank">
                        localhost/acortador/<?php echo htmlspecialchars($_GET['resultado']); ?>
                    </a>
                </div>
                <span class="badge-clicks">0 clics</span>
            </div>
        <?php endif; ?>
<h3 style="margin-top: 2.5rem; text-align: left; font-size: 1.1rem; color: var(--accent);">
    Últimos enlaces creados
</h3>

<div style="overflow-x: auto; margin-top: 1rem;">
    <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem; text-align: left;">
        <thead>
            <tr style="border-bottom: 2px solid #334155; color: var(--text-muted);">
                <th style="padding: 8px 5px;">Enlace Corto</th>
                <th style="padding: 8px 5px;">Destino Real</th>
                <th style="padding: 8px 5px; text-align: center;">Clics</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($ultimos_enlaces) == 0): ?>
                <tr>
                    <td colspan="3" style="padding: 15px 0; color: var(--text-muted); text-align: center;">
                        Aún no has acortado ningún enlace.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($ultimos_enlaces as $enlace): ?>
                    <tr style="border-bottom: 1px solid #334155;">
                        <td style="padding: 10px 5px;">
                            <a href="http://localhost/acortador/<?php echo $enlace['codigo_corto']; ?>" 
                               target="_blank" style="color: var(--accent); text-decoration: none; font-weight: bold;">
                                /<?php echo $enlace['codigo_corto']; ?>
                            </a>
                        </td>
                        <td style="padding: 10px 5px; color: var(--text-muted); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?php echo htmlspecialchars($enlace['url_original']); ?>
                        </td>
                        <td style="padding: 10px 5px; text-align: center; font-weight: bold;">
                            <?php echo $enlace['clicks']; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
    </div>
</div>

</body>
</html>