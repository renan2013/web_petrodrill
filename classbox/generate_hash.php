<?php
$new_password = 'CefiAdmin2026!';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
echo "Contraseña: CefiAdmin2026!<br>";
echo "Hash: " . $hashed_password;
?>