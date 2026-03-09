<?php
session_start();

// Include the database connection
require_once '../config/database.php';

// Function to redirect back to login with an error message
function redirect_with_error($message) {
    header('Location: login.php?error=' . urlencode($message));
    exit;
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Get username and password from the form
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    redirect_with_error('Username and password are required.');
}

// CAPTCHA verification
$user_captcha = $_POST['captcha'] ?? '';
$correct_captcha = $_SESSION['captcha_result'] ?? null;

// Clear the CAPTCHA result from session immediately after retrieval
unset($_SESSION['captcha_result']);

if (empty($user_captcha) || $user_captcha != $correct_captcha) {
    redirect_with_error('CAPTCHA incorrecto. Por favor, inténtalo de nuevo.');
}

// Prepare and execute the query to find the user
try {
    $stmt = $pdo->prepare("SELECT id_user, username, password, full_name, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Verify if user exists and password is correct
    if ($user && password_verify($password, $user['password'])) {
        // Password is correct, start the session
        session_regenerate_id(); // Prevents session fixation attacks
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role']; // Store the user's role

        // Redirect to the admin dashboard
        header('Location: ../admin/index.php');
        exit;
    } else {
        // Invalid credentials
        redirect_with_error('Invalid username or password.');
    }
} catch (PDOException $e) {
    // Database error
    // In a real-world scenario, you should log this error instead of showing it.
    redirect_with_error('A database error occurred. Please try again later.');
}
?>