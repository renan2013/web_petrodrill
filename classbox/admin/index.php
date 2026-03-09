<?php
session_start();
$page_title = '<i class="fa-solid fa-gauge-high"></i> Panel de Control';
require_once(dirname(__FILE__) . '/partials/header.php');
require_once(dirname(__FILE__) . '/../config/config.php'); // Needed for DB credentials

// --- Database Connection ---
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Logic to Get Permitted Modules ---
$user_role = $_SESSION['user_role'] ?? 'admin';
$user_id = $_SESSION['user_id'] ?? 0;
$allowed_modules = [];

if ($user_role === 'superadmin') {
    // Superadmin can see all modules
    $module_result = $conn->query("SELECT name, display_name, description FROM modules");
    while ($row = $module_result->fetch_assoc()) {
        $allowed_modules[$row['name']] = $row;
    }
} else {
    // Admin only sees their assigned modules
    $stmt = $conn->prepare(
        "SELECT m.name, m.display_name, m.description FROM modules m " . 
        "JOIN user_modules um ON m.id_module = um.id_module " . 
        "WHERE um.id_user = ?"
    );
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $allowed_modules[$row['name']] = $row;
    }
    $stmt->close();
}

$conn->close();

// --- Module Definitions (for icons and links) ---
$module_definitions = [
    'posts' => ['icon' => 'fa-newspaper', 'link' => 'posts/index.php', 'color' => 'posts-btn'],
    'admisiones' => ['icon' => 'fa-user-plus', 'link' => 'admisiones/index.php', 'color' => 'admissions-btn'],
    'menus' => ['icon' => 'fa-bars', 'link' => 'menus/index.php', 'color' => 'menus-btn'],
    'users' => ['icon' => 'fa-users', 'link' => 'usuarios/index.php', 'color' => 'users-btn'],
    'client_data' => ['icon' => 'fa-address-card', 'link' => 'client_data/index.php', 'color' => 'client-data-btn']
];

?>

<div class="dashboard-welcome">
    <h2>¡Bienvenido al Panel de Administración de Classbox!</h2>
    <p>Este es tu centro de control para gestionar todo el contenido de tu sitio web.</p>
    <p>Selecciona una opción para empezar:</p>
    <div class="module-buttons">
        <?php foreach ($allowed_modules as $name => $module_data): ?>
            <?php if (isset($module_definitions[$name])): ?>
                <a href="<?php echo $module_definitions[$name]['link']; ?>" class="module-button <?php echo $module_definitions[$name]['color']; ?>">
                    <i class="fa-solid <?php echo $module_definitions[$name]['icon']; ?>"></i>
                    <h3><?php echo htmlspecialchars($module_data['display_name']); ?></h3>
                    <p><?php echo htmlspecialchars($module_data['description']); ?></p>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if (empty($allowed_modules)): ?>
            <div class="alert alert-warning">No tienes módulos asignados. Contacta a un superadministrador.</div>
        <?php endif; ?>
    </div>
</div>

<style>
    .dashboard-welcome { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .dashboard-welcome h2 { margin-top: 0; }
    .module-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); /* Ajustado para ser más pequeño */
        gap: 15px; /* Espacio reducido */
        margin-top: 20px;
    }
    .module-button {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 15px; /* Reducido a la mitad */
        border-radius: 8px;
        text-decoration: none;
        color: white;
        font-weight: bold;
        text-align: center;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .module-button:hover {
        transform: translateY(-3px); /* Ajustado */
        box-shadow: 0 6px 12px rgba(0,0,0,0.2); /* Ajustado */
        color: white; 
    }
    .module-button i {
        font-size: 1.5em; /* Reducido a la mitad */
        margin-bottom: 10px; /* Ajustado */
    }
    .module-button h3 {
        font-size: 1em; /* Reducido a la mitad */
        margin-bottom: 5px; /* Ajustado */
    }
    .module-button p {
        font-size: 0.7em; /* Reducido a la mitad */
        opacity: 0.9;
    }

    /* Colores de los botones (revertidos a los anteriores) */
    .posts-btn { background-color: #28a745; /* Verde */ }
    .categories-btn { background-color: #17a2b8; /* Info/Celeste */ }
    .menus-btn { background-color: #007bff; /* Azul */ }
    .users-btn { background-color: #ffc107; /* Naranja */ }
    .admissions-btn { background-color: #6f42c1; /* Púrpura */ }
    .client-data-btn { background-color: #0056b3; /* Azul oscuro */ }

    /* Media query para 2 columnas en pantallas más pequeñas */
    @media (max-width: 768px) {
        .module-buttons {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); /* Ajustado para 2 columnas */
        }
    }
</style>

<?php
require_once 'partials/footer.php';
?>