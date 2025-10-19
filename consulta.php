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
 
  $tablas = ['razas', 'clases', 'personajes', 'hechizos', 'objetos_magicos'];

  echo "<html><head><title>Consulta de Datos</title></head><body>";
  echo "<h1>Datos de la base de datos RPG</h1>";

  foreach ($tablas as $tabla) {
    echo "<h2>Tabla: $tabla</h2>";
    $stmt = $pdo->query("SELECT * FROM $tabla");
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($resultados) > 0) {
      echo "<table border='1' cellpadding='5' cellspacing='0'>";
      echo "<tr>";
      foreach (array_keys($resultados[0]) as $columna) {
        echo "<th>$columna</th>";
      }
      echo "</tr>";

      foreach ($resultados as $fila) {
        echo "<tr>";
        foreach ($fila as $valor) {
          echo "<td>" . htmlspecialchars($valor) . "</td>";
        }
        echo "</tr>";
      }
      echo "</table><br>";
    } else {
        echo "<p>No hay datos en la tabla $tabla.</p>";
    }
  }

  echo "</body></html>";

} catch (PDOException $e) {
    error_log('Error de conexión PDO: ' . $e->getMessage());
    echo "Error al conectar con la base de datos: " . htmlspecialchars($e->getMessage());
    exit;
}
?>
