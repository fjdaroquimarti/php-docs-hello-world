<?php

//Función para mostrar tablas
function mostrarTabla($conexion, $tabla) {
   
}

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

   //Ejemplo: mostrar satos de la tabla razas
   $resultado = $pdo->query("SELECT * FROM razas");
   $fila = $resultado->fetch();
   echo "<p> Nombre: $fila['nombre']</p>";
   
  // Ejemplo: mostrar datos de todas las tablas
  $tablas = ["razas", "clases", "personajes", "hechizos", "objetos_magicos"];
// foreach ($tablas as $tabla) {
//    $resultado = $pdo->query("SELECT * FROM $tabla");
//    echo "<p> Numero de filas: $resultado->num_rows</p>";
//    if ($resultado->num_rows > 0) {
//        echo "<h2>Tabla: $tabla</h2><table border='1'><tr>";
//        while ($campo = $resultado->fetch_field()) {
//            echo "<th>{$campo->name}</th>";
//        }
//        echo "</tr>";
//        while ($fila = $resultado->fetch_assoc()) {
//            echo "<tr>";
//            foreach ($fila as $valor) {
//                echo "<td>$valor</td>";
//            }
//            echo "</tr>";
//        }
//        echo "</table><br>";
//    } else {
//        echo "<p>No hay datos en la tabla $tabla.</p>";
//    }
//  }
} catch (PDOException $e) {
  error_log('Error de conexión PDO: ' . $e->getMessage());
  echo "Error al conectar con la base de datos: " . htmlspecialchars($e->getMessage());
  exit;
}
