<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Every admin page will start with this.
require_once(dirname(__FILE__) . '/../../config/config.php');
require_once(dirname(__FILE__) . '/../../auth/check_auth.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Classbox</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 0; background-color: #f9fafb; }
        .admin-container { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background-color: #1f2937; color: #fff; padding: 20px; }
        .sidebar h2 { color: #fff; text-align: center; margin-bottom: 30px; }
        .sidebar a { display: block; color: #d1d5db; text-decoration: none; padding: 12px 15px; border-radius: 6px; margin-bottom: 8px; }
        .sidebar a:hover, .sidebar a.active { background-color: #254a66; color: #fff; }
        .sidebar-divider { border: none; border-top: 1px solid #4a5568; margin: 20px 0; }
        .sidebar a i { margin-right: 8px; }
        .main-content { flex-grow: 1; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e5e7eb; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; }
        .user-info a { color: #007bff; text-decoration: none; }
        .user-info a:hover { text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background-color: #f3f4f6; }
        .actions a { margin-right: 10px; color: #007bff; text-decoration: none; }
        .actions a.delete { color: #dc3545; }
        .container {
            width: 100%;
            margin: 0;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .form-control:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 .2rem rgba(0,123,255,.25);
        }
        .btn {
            display: inline-block;
            font-weight: 400;
            color: #212529;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: .25rem;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            color: #fff;
            background-color: #0069d9;
            border-color: #0062cc;
        }
        .alert {
            position: relative;
            padding: .75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: .25rem;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        /* Styles for forms */
        .styled-form { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); width: 100%; margin-left: 0; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 8px; }
        .form-group input[type="text"], .form-group input[type="password"], .form-group input[type="number"], .form-group select, .form-group textarea, .form-group input[type="file"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .form-group small { color: #666; font-size: 0.9em; }
        .form-actions { margin-top: 30px; text-align: right; }
        .btn-submit { background-color: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-submit:hover { background-color: #0056b3; }
        .btn-cancel { color: #6c757d; text-decoration: none; margin-left: 15px; }
        .error-message { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .alert-success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }

        /* Styles for table header and create button (from posts/index.php) */
        .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-create { background-color: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; }
        .btn-create:hover { background-color: #218838; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <img src="<?php echo BASE_URL; ?>/admin/assets/img/logo_classbox_login.svg" alt="Classbox Logo" style="width: 100%; margin-bottom: 5px;">
            <p style="color: #d1d5db; text-align: center; font-size: 0.8em; margin-bottom: 20px;">by renangalvan.net</p>
            <hr class="sidebar-divider">
            <a href="<?php echo BASE_URL; ?>/admin/index.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
            <?php
            // --- Logic to Get Permitted Modules for Sidebar ---
            $user_role_sidebar = $_SESSION['user_role'] ?? 'admin';
            $user_id_sidebar = $_SESSION['user_id'] ?? 0;
            $allowed_modules_sidebar = [];

            $conn_sidebar = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($user_role_sidebar === 'superadmin') {
                $module_result_sidebar = $conn_sidebar->query("SELECT name, display_name FROM modules");
                while ($row = $module_result_sidebar->fetch_assoc()) {
                    $allowed_modules_sidebar[$row['name']] = $row;
                }
            } else {
                $stmt_sidebar = $conn_sidebar->prepare(
                    "SELECT m.name, m.display_name FROM modules m " . 
                    "JOIN user_modules um ON m.id_module = um.id_module " . 
                    "WHERE um.id_user = ?"
                );
                $stmt_sidebar->bind_param('i', $user_id_sidebar);
                $stmt_sidebar->execute();
                $result_sidebar = $stmt_sidebar->get_result();
                while ($row = $result_sidebar->fetch_assoc()) {
                    $allowed_modules_sidebar[$row['name']] = $row;
                }
                $stmt_sidebar->close();
            }

            $conn_sidebar->close();

            // --- Module Definitions for Sidebar ---
            $module_links = [
                'posts' => ['icon' => 'fa-newspaper', 'link' => 'posts/index.php', 'title' => 'Publicaciones'],
                'galerias' => ['icon' => 'fa-images', 'link' => 'galerias/index.php', 'title' => 'Graduaciones'],
                'testimonios' => ['icon' => 'fa-comment-dots', 'link' => 'testimonios/index.php', 'title' => 'Testimonios'],
                'admisiones' => ['icon' => 'fa-user-plus', 'link' => 'admisiones/index.php', 'title' => 'Admisiones'],
                'menus' => ['icon' => 'fa-bars', 'link' => 'menus/index.php', 'title' => 'Menús'],
                'users' => ['icon' => 'fa-users', 'link' => 'usuarios/index.php', 'title' => 'Usuarios'],
                'client_data' => ['icon' => 'fa-address-card', 'link' => 'client_data/index.php', 'title' => 'Datos Cliente']
            ];

            foreach ($allowed_modules_sidebar as $name => $module_data) {
                if (isset($module_links[$name])) {
                    echo '<a href="' . BASE_URL . '/admin/' . $module_links[$name]['link'] . '"><i class="fa-solid ' . $module_links[$name]['icon'] . '"></i> ' . htmlspecialchars($module_links[$name]['title']) . '</a>';
                }
            }
            ?>
            <hr class="sidebar-divider">
            <?php if ($user_role_sidebar === 'superadmin'): ?>
                <a href="<?php echo BASE_URL; ?>/admin/manage_permissions.php"><i class="fa-solid fa-shield-halved"></i> Gestionar Permisos</a>
            <?php endif; ?>
        </div>
        <div class="main-content">
            <div class="header">
                <h1><?php echo $page_title ?? 'Dashboard'; ?></h1>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</span> &nbsp;
                    <a href="<?php echo BASE_URL; ?>/auth/logout.php">Logout</a>
                </div>
            </div>
            <div class="content-area">