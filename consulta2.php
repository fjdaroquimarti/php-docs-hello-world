<?php

// Recuperar variables de entorno
$dbHost = getenv('DB_HOST');
$dbName = "rpg";
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASSWORD');

if (!$dbHost || !$dbUser || $dbPass === false) {
  throw new \RuntimeException('Faltan variables de entorno para la conexión a la base de datos.');
}
// DSN con charset utf8mb4
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
try {
  $options = [
  // Excepciones en errores
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  // Fetch como array asociativo
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  // Desactivar emulación de prepares
  PDO::ATTR_EMULATE_PREPARES => false,
  // Asegurar la conexión TLS hacia Azure Database for MySQL
  PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/BaltimoreCyberTrustRoot.crt.pem',
  // Desactivamos la validación del certificado SSL
  PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
  ];
  // Crear la conexión PDO
  $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
  error_log('Error de conexión PDO: ' . $e->getMessage());
  echo "Error al conectar con la base de datos: " . htmlspecialchars($e->getMessage());
  exit;
}
  
$tablas = ['razas', 'clases', 'personajes', 'hechizos', 'objetos_magicos'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tabla'])) {
    $tabla = $_POST['tabla'];
    if (in_array($tabla, $tablas)) {
        $stmt = $pdo->query("SELECT * FROM $tabla");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta de tablas</title>
</head>
<body>
    <h2>Selecciona una tabla para consultar sus datos</h2>
    <form method="post">
        <select name="tabla">
            <?php foreach ($tablas as $t): ?>
                <option value="<?= $t ?>" <?= isset($tabla) && $tabla === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Consultar">
    </form>

    <?php if (!empty($datos)): ?>
        <h3>Datos de la tabla <?= htmlspecialchars($tabla) ?>:</h3>
        <table border="1">
            <tr>
                <?php foreach (array_keys($datos[0]) as $columna): ?>
                    <th><?= htmlspecialchars($columna) ?></th>
                <?php endforeach; ?>
            </tr>
            <?php foreach ($datos as $fila): ?>
                <tr>
                    <?php foreach ($fila as $valor): ?>
                        <td><?= htmlspecialchars($valor) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif (isset($tabla)): ?>
        <p>No hay datos en la tabla seleccionada.</p>
    <?php endif; ?>
</body>
</html>
