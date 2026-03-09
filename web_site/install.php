
<?php
// Simple Installer for the Public Website

// --- Security Check ---
if (file_exists('config/config.php')) {
    die("Installer detected an existing configuration file. To prevent accidental changes, the installation process has been stopped. Please remove 'config/config.php' if you wish to reinstall.");
}

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$errors = [];

// --- Main Installation Logic ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 2) {
        // Get DB credentials from form
        $db_host = $_POST['db_host'] ?? '';
        $db_name = $_POST['db_name'] ?? '';
        $db_user = $_POST['db_user'] ?? '';
        $db_pass = $_POST['db_pass'] ?? '';

        // --- 1. Test Database Connection ---
        try {
            $conn = new mysqli($db_host, $db_user, $db_pass);
            if ($conn->connect_error) {
                throw new Exception($conn->connect_error);
            }
        } catch (Exception $e) {
            $errors[] = "Database connection failed: " . $e->getMessage();
        }

        // --- 2. Create Database and Tables ---
        if (empty($errors)) {
            $conn->query("CREATE DATABASE IF NOT EXISTS `$db_name`");
            $conn->select_db($db_name);

            $sql_schema = file_get_contents('database_schema.sql');
            if ($conn->multi_query($sql_schema)) {
                while ($conn->next_result()) {;}
            } else {
                $errors[] = "Error creating database tables: " . $conn->error;
            }
        }

        // --- 3. Generate config.php ---
        if (empty($errors)) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            $script_path = str_replace('/install.php', '', $_SERVER['SCRIPT_NAME']);
            $base_url = "{$protocol}://{$host}{$script_path}";

            $config_content = "<?php
// -- DATABASE CONNECTION --
define('DB_HOST', '{$db_host}');
define('DB_USER', '{$db_user}');
define('DB_PASS', '{$db_pass}');
define('DB_NAME', '{$db_name}');
define('DB_CHARSET', 'utf8mb4');

// -- APPLICATION PATHS --
define('BASE_URL', '{$base_url}'); 
define('ROOT_PATH', __DIR__);

// -- OTHER SETTINGS --
define('SITE_NAME', 'My Website');
?>";
            
            if (!is_dir('config')) {
                mkdir('config', 0755, true);
            }

            if (!file_put_contents('config/config.php', $config_content)) {
                $errors[] = "Error writing config file. Please check file permissions for the 'config' directory.";
            } else {
                rename('install.php', 'install.php.locked');
                header("Location: index.php?installed=true");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Installer</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; padding: 20px 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #2c3e50; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { display: inline-block; background-color: #3498db; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 16px; }
        .btn:hover { background-color: #2980b9; }
        .error { background-color: #e74c3c; color: white; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error ul { margin: 0; padding-left: 20px; }
        .note { font-size: 0.9em; color: #7f8c8d; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Website Installer</h1>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <strong>Please fix the following errors:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <h2>Step 1: Welcome</h2>
            <p>This wizard will guide you through the setup process for the public website.</p>
            <p>Please have your database credentials ready.</p>
            <a href="?step=2" class="btn">Start Installation</a>
        <?php elseif ($step == 2): ?>
            <form action="install.php?step=2" method="post">
                <h2>Step 2: Database Configuration</h2>
                <div class="form-group">
                    <label for="db_host">Database Host</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                </div>
                <div class="form-group">
                    <label for="db_name">Database Name</label>
                    <input type="text" id="db_name" name="db_name" required>
                </div>
                <div class="form-group">
                    <label for="db_user">Database User</label>
                    <input type="text" id="db_user" name="db_user" required>
                </div>
                <div class="form-group">
                    <label for="db_pass">Database Password</label>
                    <input type="password" id="db_pass" name="db_pass">
                </div>
                <button type="submit" class="btn">Install Now</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
