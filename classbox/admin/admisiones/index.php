<?php
session_start();
$page_title = 'Administrar Solicitudes de Admisión';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/database.php';

$search_query = $_GET['search'] ?? '';

$sql = "
    SELECT 
        id_matricula, 
        nombre, 
        programa, 
        nacionalidad, 
        foto, 
        email, 
        whatsapp, 
        documentos, 
        fecha_nacimiento, 
        fecha_solicitud 
    FROM formulario_matricula
";

$params = [];

if (!empty($search_query)) {
    $sql .= " WHERE nombre LIKE ? OR programa LIKE ? OR nacionalidad LIKE ? OR email LIKE ?";
    $params = ['%' . $search_query . '%', '%' . $search_query . '%', '%' . $search_query . '%', '%' . $search_query . '%'];
}

$sql .= " ORDER BY fecha_solicitud DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$solicitudes = $stmt->fetchAll();

$solicitud_count = count($solicitudes);
?>

<div>
    <h2>Listado de Solicitudes de Admisión</h2>
    <div class="table-header">
        <div class="search-column">
            <form action="" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Buscar solicitudes..." value="<?php echo htmlspecialchars($search_query); ?>" class="form-control search-input">
                <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
            </form>
        </div>
        <div class="create-column">
            <a href="/learner/solicitud_matricula.php" class="btn-create" target="_blank">+ Nueva Matrícula</a>
        </div>
    </div>

    <p>Total de solicitudes: <?php echo $solicitud_count; ?></p>

    <?php if (empty($solicitudes)): ?>
        <p>No hay solicitudes de admisión por el momento.</p>
    <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Programa</th>
                        <th>Nacionalidad</th>
                        <th>Email</th>
                        <th>WhatsApp</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Fecha de Solicitud</th>
                        <th>Foto</th>
                        <th>Documento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 1; foreach ($solicitudes as $solicitud): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($solicitud['id_matricula']); ?></td>
                            <td><?php echo htmlspecialchars($solicitud['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($solicitud['programa']); ?></td>
                            <td><?php echo htmlspecialchars($solicitud['nacionalidad']); ?></td>
                            <td><?php echo htmlspecialchars($solicitud['email']); ?></td>
                            <td><?php echo htmlspecialchars($solicitud['whatsapp']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($solicitud['fecha_nacimiento']))); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud']))); ?></td>
                            <td>
                                <a href="/learner/<?php echo htmlspecialchars($solicitud['foto']); ?>" target="_blank">Ver Foto</a>
                            </td>
                            <td>
                                <a href="/learner/<?php echo htmlspecialchars($solicitud['documentos']); ?>" target="_blank">Ver PDF</a>
                            </td>
                            <td class="actions">
                                <a href="editar_matricula.php?id=<?php echo htmlspecialchars($solicitud['id_matricula']); ?>" class="btn-edit" onclick="console.log('Clic en Editar para ID: <?php echo htmlspecialchars($solicitud['id_matricula']); ?>');"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
                                <a href="eliminar_matricula.php?id=<?php echo htmlspecialchars($solicitud['id_matricula']); ?>" class="delete" onclick="console.log('Clic en Eliminar para ID: <?php echo htmlspecialchars($solicitud['id_matricula']); ?>'); return confirm('¿Estás seguro de que quieres eliminar esta solicitud?');"><i class="fa-solid fa-trash"></i> Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
    <?php endif; ?>
</div>

<style>
.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.search-column {
    flex-grow: 1;
    margin-right: 20px; /* Espacio entre el buscador y el botón */
}

.search-form {
    display: flex;
    gap: 10px;
}

.search-input {
    flex-grow: 1;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.btn-search {
    background: linear-gradient(135deg, #2D8FE2, #1A74D2);
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
}

.btn-search:hover {
    background-color: #0056b3;
}

.btn-create {
    background-color: #28a745;
    color: white;
    padding: 10px 15px;
    text-decoration: none;
    border-radius: 5px;
}

.btn-create:hover {
    background-color: #218838;
}

/* Estilos para los botones de acción en la tabla */
.actions a {
    margin-right: 5px;
    padding: 5px 10px;
    border-radius: 4px;
    text-decoration: none;
    color: white;
}

.actions .btn-edit {
    background-color: #007bff;
}

.actions .btn-edit:hover {
    background-color: #0056b3;
}

.actions .delete {
    background-color: #dc3545;
    color: white !important;
}

.actions .delete:hover {
    background-color: #c82333;
}
</style>