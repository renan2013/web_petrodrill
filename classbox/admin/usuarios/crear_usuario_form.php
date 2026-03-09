<?php
session_start();
$page_title = 'Crear Nuevo Usuario';
require_once(dirname(__FILE__) . '/../partials/header.php'); // Adjust path as needed
?>

<form id="createUserForm" class="styled-form" method="POST"> <!-- Added styled-form class -->
    <div id="responseMessage" class="error-message" style="display: none;"></div> <!-- Changed to error-message and hidden by default -->

    <div class="form-group">
        <label for="username">Nombre de Usuario</label>
        <input type="text" id="username" name="username" required class="form-control">
        <small>El nombre de usuario único para el nuevo administrador.</small>
    </div>

    <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required class="form-control">
        <small>La contraseña para la nueva cuenta de administrador.</small>
    </div>

    <div class="form-group">
        <label for="full_name">Nombre Completo</label>
        <input type="text" id="full_name" name="full_name" class="form-control">
        <small>El nombre completo del administrador (opcional).</small>
    </div>

    <div class="form-actions"> <!-- Added form-actions div -->
        <button type="submit" class="btn-submit">Crear Usuario</button>
        <a href="../index.php" class="btn-cancel">Cancelar</a>
    </div>
</form>

<script>
document.getElementById('createUserForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    const form = event.target;
    const formData = new FormData(form);
    const responseMessageDiv = document.getElementById('responseMessage');
    responseMessageDiv.style.display = 'none'; // Hide previous messages

    fetch('crear_usuario.php', { // Path to the script that processes the form
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            responseMessageDiv.className = 'error-message alert-success'; // Use error-message for styling
            form.reset(); // Clear the form on success
        } else {
            responseMessageDiv.className = 'error-message alert-danger'; // Use error-message for styling
        }
        responseMessageDiv.textContent = data.message;
        responseMessageDiv.style.display = 'block'; // Show the message
    })
    .catch(error => {
        console.error('Error:', error);
        responseMessageDiv.className = 'error-message alert-danger';
        responseMessageDiv.textContent = 'Ocurrió un error al crear el usuario.';
        responseMessageDiv.style.display = 'block'; // Show the message
    });
});
</script>

<?php
require_once(dirname(__FILE__) . '/../partials/footer.php'); // Adjust path as needed
?>