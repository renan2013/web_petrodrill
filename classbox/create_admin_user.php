<?php
// --- Create Admin User ---
// This script should be run once from the command line or browser
// to create the initial admin user. 
// IMPORTANT: Delete this file after running it for security reasons.

require_once 'config/database.php';

// --- Configuration ---
$admin_username = 'admin';
$admin_password = 'admin'; 
$admin_fullname = 'Administrador Sistema';
$admin_role = 'superadmin';

// --- Logic ---
echo "Attempting to create admin user...\n";

// Hash the password securely
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

// Check if user already exists
try {
    $stmt = $pdo->prepare("SELECT id_user FROM users WHERE username = ?");
    $stmt->execute([$admin_username]);
    if ($stmt->fetch()) {
        echo "Error: User '$admin_username' already exists.\n";
        exit;
    }

    // Insert the new admin user
    $stmt = $pdo->prepare(
        "INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$admin_username, $hashed_password, $admin_fullname, $admin_role]);
    $new_user_id = $pdo->lastInsertId();

    // Assign all existing modules to the new superadmin
    $stmt = $pdo->query("SELECT id_module FROM modules");
    $modules = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($modules)) {
        $stmt_mod = $pdo->prepare("INSERT INTO user_modules (id_user, id_module) VALUES (?, ?)");
        foreach ($modules as $id_module) {
            $stmt_mod->execute([$new_user_id, $id_module]);
        }
    }

    echo "--------------------------------------------------\n";
    echo "Success! Admin user created and permissions assigned.\n";
    echo "Username: " . $admin_username . "\n";
    echo "Password: " . $admin_password . "\n";
    echo "--------------------------------------------------\n";
    echo "IMPORTANT: You should now delete this file (create_admin_user.php) for security!\n";

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

?>