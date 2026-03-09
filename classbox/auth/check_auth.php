<?php
// --- Authentication Check ---
// This script is included at the top of all admin pages to protect them.

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user_id session variable is set
if (!isset($_SESSION['user_id'])) {
    // If not set, the user is not logged in. Redirect to the login page.
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
} else {
    
}

// If the script reaches this point, the user is authenticated.
// We can define a constant to easily check for auth status if needed.
define('IS_AUTHENTICATED', true);