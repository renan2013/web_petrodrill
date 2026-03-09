<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once(dirname(__FILE__) . '/../config/config.php');

if (isset($_SESSION['user_id'])) {
    header('Location: ../admin/index.php');
    exit;
}

$error = $_GET['error'] ?? '';

$num1 = rand(1, 10);
$num2 = rand(1, 10);
$operator = (rand(0, 1) == 0) ? '+' : '-';
$captcha_result = ($operator == '+') ? $num1 + $num2 : $num1 - $num2;
$_SESSION['captcha_result'] = $captcha_result;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!-- Open Graph (Facebook, WhatsApp) -->
<meta property="og:title" content="Login - Classbox">
<meta property="og:description" content="Accede al gestor de contenidos de Classbox.">
<meta property="og:image" content="<?php echo BASE_URL; ?>/assets/images/logo.svg">
<meta property="og:url" content="<?php echo BASE_URL; ?>/auth/login.php">
<meta property="og:type" content="website">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Login - Classbox">
<meta name="twitter:description" content="Accede al gestor de contenidos de Classbox.">
<meta name="twitter:image" content="<?php echo BASE_URL; ?>/assets/images/logo.svg">


    <title>Login - Classbox</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f4f5f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 320px;
            text-align: center;
            position: relative;
            transition: box-shadow 0.3s ease;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #2D8FE2, #1A74D2);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(45, 143, 226, 0.3);
            transition: all 0.3s ease;
        }

        button:hover {
            background: linear-gradient(135deg, #499DF0, #2D8FE2);
            transform: scale(1.03);
            box-shadow: 0 6px 16px rgba(45, 143, 226, 0.4);
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-top: 20px;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeZoom {
            0% { opacity: 0; transform: scale(0.8); }
            100% { opacity: 1; transform: scale(1); }
        }

        .logo-animated {
            animation: fadeZoom 1s ease-out forwards;
            opacity: 0;
            display: block;
            margin: 0 auto 20px auto;
            max-width: 300px;
        }

        .footer {
            margin-top: 20px;
            padding: 10px;
            color: #666;
            font-size: 0.85em;
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
            100% { transform: translateX(0); }
        }

        .shake-error {
            animation: shake 0.4s ease;
            box-shadow: 0 0 0 4px rgba(255, 0, 0, 0.2);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 400px) {
            .login-container {
                width: 90%;
                padding: 24px;
            }

            .logo-animated {
                max-width: 330px;
                margin-bottom: 16px;
            }

            button {
                font-size: 15px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container <?php echo $error ? 'shake-error' : ''; ?>">
        <img src="<?php echo BASE_URL; ?>/admin/assets/img/logo_classbox_login.svg" alt="Classbox Logo" class="logo-animated">
        <strong>Gestor de contenidos web</strong><br/><br/>
        <form action="login_process.php" method="POST">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" required />
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required />
            </div>
            <div class="form-group">
                <label for="captcha">¿Cuánto es <?php echo "$num1 $operator $num2"; ?>?</label>
                <input type="text" id="captcha" name="captcha" required />
            </div>
            <button type="submit">Login</button>
        </form>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="footer">
            by renangalvan.net (+506) 87777849<br/>San José, Costa Rica - 2025
        </div>
    </div>
</body>
</html>
