<?php
session_start();
$page_title = 'Gestor de Testimonios';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/database.php';

try {
    $stmt = $pdo->query("SELECT * FROM testimonios ORDER BY created_at DESC");
    $testimonios = $stmt->fetchAll();
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
}
?>

<div class="table-header">
    <h3>Testimonios de Estudiantes</h3>
    <a href="create.php" class="btn-create">+ Añadir Testimonio</a>
</div>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Profesión</th>
            <th>Comentario</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($testimonios)): ?>
            <tr>
                <td colspan="5" style="text-align:center;">No hay testimonios registrados.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($testimonios as $t): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($t['nombre']); ?></strong></td>
                    <td>
                        <?php if (!empty($t['video_iframe'])): ?>
                            <span class="badge bg-primary" style="padding: 3px 6px; border-radius: 4px; color: white; font-size: 0.8em; background-color: #007bff;">VIDEO</span>
                        <?php else: ?>
                            <span class="badge bg-secondary" style="padding: 3px 6px; border-radius: 4px; color: white; font-size: 0.8em; background-color: #6c757d;">FOTO</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($t['profesion']); ?></td>
                    <td><?php echo htmlspecialchars(substr($t['comentario'], 0, 50)) . '...'; ?></td>
                    <td class="actions">
                        <a href="edit.php?id=<?php echo $t['id_testimonio']; ?>">Editar</a>
                        <button type="button" class="btn-link delete" style="background:none; border:none; color:#dc3545; cursor:pointer;" onclick="confirmDelete(<?php echo $t['id_testimonio']; ?>, '<?php echo addslashes($t['nombre']); ?>')">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success')) Swal.fire({ icon: 'success', title: '¡Hecho!', text: urlParams.get('success'), timer: 3000, showConfirmButton: false });
});

function confirmDelete(id, nombre) {
    Swal.fire({
        title: '¿Eliminar testimonio?',
        text: `Vas a borrar el comentario de "${nombre}".`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) window.location.href = `delete.php?id=${id}`;
    });
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>