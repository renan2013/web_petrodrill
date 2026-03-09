<?php
session_start();
$page_title = 'Administrar Publicaciones';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/database.php';

$search_query = $_GET['search'] ?? '';

$sql = "
    SELECT 
        p.id_post, 
        p.title, 
        p.created_at, 
        c.name as category_name, 
        u.full_name as author_name 
    FROM posts p
    JOIN categories c ON p.id_category = c.id_category
    LEFT JOIN users u ON p.id_user = u.id_user
";

$params = [];
$where_clauses = [];

if (!empty($search_query)) {
    $where_clauses[] = "(p.title LIKE ? OR p.content LIKE ? OR c.name LIKE ?)";
    $params = ['%' . $search_query . '%', '%' . $search_query . '%', '%' . $search_query . '%'];
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

$post_count = count($posts);
?>

<div class="table-header">
    <form action="" method="GET" class="search-form">
        <input type="text" name="search" placeholder="Buscar publicaciones..." value="<?php echo htmlspecialchars($search_query); ?>" class="form-control search-input">
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
    </form>
    <a href="create.php" class="btn-create">+ Crear Nueva Publicación</a>
</div>

<p>Total de publicaciones: <?php echo $post_count; ?></p>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Título</th>
            <th>Categoría</th>
            <th>Publicado El</th>
            <th>Autor</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($posts)): ?>
            <tr>
                <td colspan="6" style="text-align:center;">No se encontraron publicaciones.</td>
            </tr>
        <?php else: ?>
            <?php $counter = 1; foreach ($posts as $post): ?>
                <tr>
                    <td><?php echo $counter++; ?></td>
                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                    <td><?php echo htmlspecialchars($post['category_name']); ?></td>
                    <td><?php echo date('F j, Y', strtotime($post['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($post['author_name'] ?? 'Desconocido'); ?></td>
                    <td class="actions">
                        <a href="attachments.php?post_id=<?php echo $post['id_post']; ?>" class="btn-attach" title="Adjuntos"><i class="fa-solid fa-paperclip"></i></a>
                        <a href="edit.php?id=<?php echo $post['id_post']; ?>" class="btn-edit-icon" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                        <button type="button" class="btn-delete-icon" title="Eliminar" onclick="confirmDelete(<?php echo $post['id_post']; ?>, '<?php echo addslashes($post['title']); ?>')"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<script>
// Show alerts based on URL parameters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');

    if (success) {
        Swal.fire({ icon: 'success', title: '¡Hecho!', text: success, timer: 3000, showConfirmButton: false });
    }
    if (error) {
        Swal.fire({ icon: 'error', title: 'Error', text: error });
    }
});

function confirmDelete(id, title) {
    Swal.fire({
        title: '¿Eliminar publicación?',
        text: `Vas a borrar "${title}" y todos sus archivos adjuntos. Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `delete.php?id=${id}`;
        }
    });
}
</script>

<style>
.table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.search-form { display: flex; flex-grow: 1; gap: 10px; margin-right: 20px; } /* Added flex-grow and margin-right */
.search-input { flex-grow: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
.btn-search { background: linear-gradient(135deg, #2D8FE2, #1A74D2); padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer;text-decoration: none; color: white; }
.btn-search:hover { background-color: #0056b3; }
.btn-create { background-color: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; }
.btn-create:hover { background-color: #218838; }
.btn-attach {
  background: linear-gradient(135deg, #2D8FE2, #1A74D2);
  color: #fff !important; /* Forzamos blanco siempre */
  padding: 6px 12px;
  text-decoration: none;
  border-radius: 6px;
  font-size: 0.9em;
  box-shadow: 0 2px 6px rgba(45, 143, 226, 0.2);
  transition: all 0.3s ease;
}

.btn-attach:hover {
  background: linear-gradient(135deg, #499DF0, #2D8FE2);
  transform: scale(1.03);
  box-shadow: 0 4px 10px rgba(45, 143, 226, 0.3);
  color: #fff !important;
}

.btn-edit-icon {
  background-color: #ffc107;
  color: #000 !important;
  padding: 6px 10px;
  text-decoration: none;
  border-radius: 6px;
  font-size: 0.9em;
  transition: all 0.3s ease;
  display: inline-block;
}

.btn-edit-icon:hover {
  background-color: #e0a800;
  transform: scale(1.05);
}

.btn-delete-icon {
  background-color: #dc3545;
  color: #fff !important;
  padding: 6px 10px;
  border: none;
  border-radius: 6px;
  font-size: 0.9em;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-block;
}

.btn-delete-icon:hover {
  background-color: #c82333;
  transform: scale(1.05);
}

.actions {
  display: flex;
  gap: 5px;
  align-items: center;
}
</style>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>