<?php
session_start();
require_once __DIR__ . '/../../auth/check_auth.php';
require_once __DIR__ . '/../../config/database.php';

$page_title = 'Datos del Cliente';

// Fetch existing client data
$client_data = [];
try {
    $stmt = $pdo->query("SELECT * FROM datos_cliente LIMIT 1");
    $client_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$client_data) {
        // If no data exists, initialize with empty values
        $client_data = [
            'id_cliente_data' => null,
            'logo_path' => '',
            'whatsapp_country_code' => '',
            'whatsapp_number' => '',
            'facebook_url' => '',
            'youtube_url' => '',
            'instagram_url' => '',
            'tiktok_url' => '',
            'address' => '',
            'google_maps_url' => '',
            'email' => '',
            'phone' => '',
        ];
    }
} catch (PDOException $e) {
    error_log("Error fetching client data: " . $e->getMessage());
    $_SESSION['error_message'] = "Error al cargar los datos del cliente.";
    $client_data = [
        'id_cliente_data' => null,
        'logo_path' => '',
        'whatsapp_country_code' => '',
        'whatsapp_number' => '',
        'facebook_url' => '',
        'youtube_url' => '',
        'instagram_url' => '',
        'tiktok_url' => '',
        'address' => '',
        'google_maps_url' => '',
        'email' => '',
        'phone' => '',
    ];
}

require_once __DIR__ . '/../partials/header.php';
?>

<div class="container-fluid">
    

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            
        </div>
        <div class="card-body">
            <form action="process.php" method="POST" enctype="multipart/form-data" class="styled-form">
                <input type="hidden" name="id_cliente_data" value="<?php echo htmlspecialchars($client_data['id_cliente_data'] ?? ''); ?>">

                <div class="form-group">
                    <label for="logo_path">Logo (Subir Nuevo)</label>
                    <?php if (!empty($client_data['logo_path'])): ?>
                        <div class="mb-2">
                            <img src="<?php echo BASE_URL; ?>/<?php echo htmlspecialchars($client_data['logo_path']); ?>" alt="Logo Actual" style="max-width: 200px; height: auto;">
                            <p class="text-muted">Logo actual</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="logo_path" name="logo_file">
                    <small class="form-text text-muted">Sube un nuevo logo. Si no subes uno, se mantendrá el actual.</small>
                </div>

                <div class="form-group">
                    <label for="whatsapp_country_code">Código de País WhatsApp</label>
                    <input type="text" class="form-control" id="whatsapp_country_code" name="whatsapp_country_code" value="<?php echo htmlspecialchars($client_data['whatsapp_country_code']); ?>">
                </div>
                <div class="form-group">
                    <label for="whatsapp_number">Número de WhatsApp</label>
                    <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" value="<?php echo htmlspecialchars($client_data['whatsapp_number']); ?>">
                </div>

                <div class="form-group">
                    <label for="facebook_url">URL de Facebook</label>
                    <input type="url" class="form-control" id="facebook_url" name="facebook_url" value="<?php echo htmlspecialchars($client_data['facebook_url']); ?>">
                </div>
                <div class="form-group">
                    <label for="youtube_url">URL de YouTube</label>
                    <input type="url" class="form-control" id="youtube_url" name="youtube_url" value="<?php echo htmlspecialchars($client_data['youtube_url']); ?>">
                </div>
                <div class="form-group">
                    <label for="instagram_url">URL de Instagram</label>
                    <input type="url" class="form-control" id="instagram_url" name="instagram_url" value="<?php echo htmlspecialchars($client_data['instagram_url']); ?>">
                </div>
                <div class="form-group">
                    <label for="tiktok_url">URL de TikTok</label>
                    <input type="url" class="form-control" id="tiktok_url" name="tiktok_url" value="<?php echo htmlspecialchars($client_data['tiktok_url']); ?>">
                </div>

                <div class="form-group">
                    <label for="address">Dirección</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($client_data['address']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="google_maps_url">URL de Google Maps (Ubicación)</label>
                    <input type="url" class="form-control" id="google_maps_url" name="google_maps_url" value="<?php echo htmlspecialchars($client_data['google_maps_url']); ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email de Contacto</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($client_data['email']); ?>">
                </div>
                <div class="form-group">
                    <label for="phone">Teléfono de Contacto</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($client_data['phone']); ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>