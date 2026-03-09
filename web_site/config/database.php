<?php
// --- PDO Database Connection ---

// Configuración directa para Hostinger
define('DB_HOST', 'localhost');
define('DB_USER', 'u400283574_petro');
define('DB_PASS', 'Petro2026!');
define('DB_NAME', 'u400283574_petrodrill2026');
define('DB_CHARSET', 'utf8mb4');

// Data Source Name (DSN)
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

// PDO options for the connection
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
];

try {
    // Create a new PDO instance using constants from config.php
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    // If connection fails, stop the script and show a user-friendly error.
    die("Database connection failed. Please check your config.php settings. Error: " . $e->getMessage());
}

// The $pdo object is now ready to be used by any script that includes this file.
?>

