<?php
session_start();

// --- Authentication Check ---
if (!isset($_SESSION['user_id'])) {
    header('Location: /classbox/auth/login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$page_title = 'Editar Solicitud de Matrícula';
$error = '';
$success_message = '';

$matricula_id = $_GET['id'] ?? null;

if (!$matricula_id) {
    header('Location: index.php?error=' . urlencode('ID de matrícula no especificado.'));
    exit;
}

// Fetch current matrícula data
try {
    $stmt = $pdo->prepare("SELECT * FROM formulario_matricula WHERE id_matricula = ?");
    $stmt->execute([$matricula_id]);
    $matricula = $stmt->fetch();

    if (!$matricula) {
        header('Location: index.php?error=' . urlencode('Solicitud no encontrada.'));
        exit;
    }
} catch (PDOException $e) {
    die("Error al cargar la solicitud: " . $e->getMessage());
}

// Handle form submission for updating
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $programa = $_POST['programa'] ?? '';
    $nacionalidad = $_POST['nacionalidad'] ?? '';
    $email = $_POST['email'] ?? '';
    $whatsapp = $_POST['whatsapp'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $current_foto_path = $_POST['current_foto_path'] ?? '';
    $current_documentos_path = $_POST['current_documentos_path'] ?? '';

    // --- Validación ---
    if (empty($nombre) || empty($programa) || empty($nacionalidad) || empty($email) || empty($whatsapp) || empty($fecha_nacimiento)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } else {
        $foto_db_path = $current_foto_path;
        $doc_db_path = $current_documentos_path;

        // Directorios de subida (en el proyecto learner)
        $upload_dir_fotos = __DIR__ . '/../../../learner/uploads/fotos/';
        $upload_dir_docs = __DIR__ . '/../../../learner/uploads/documentos/';

        // Procesar nueva foto
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            // Eliminar foto antigua si existe
            if (!empty($current_foto_path)) {
                $old_foto_path = realpath(__DIR__ . '/../../../learner/' . $current_foto_path);
                if ($old_foto_path && file_exists($old_foto_path)) {
                    unlink($old_foto_path);
                }
            }
            $foto_name = uniqid('foto_', true) . '-' . basename($_FILES['foto']['name']);
            $foto_path = $upload_dir_fotos . $foto_name;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $foto_path)) {
                $foto_db_path = 'uploads/fotos/' . $foto_name;
            } else {
                $error = 'Error al subir la nueva foto.';
            }
        }

        // Procesar nuevo documento
        if (isset($_FILES['documentos']) && $_FILES['documentos']['error'] === UPLOAD_ERR_OK) {
            // Eliminar documento antiguo si existe
            if (!empty($current_documentos_path)) {
                $old_doc_path = realpath(__DIR__ . '/../../../learner/' . $current_documentos_path);
                if ($old_doc_path && file_exists($old_doc_path)) {
                    unlink($old_doc_path);
                }
            }
            $doc_name = uniqid('doc_', true) . '-' . basename($_FILES['documentos']['name']);
            $doc_path = $upload_dir_docs . $doc_name;
            if (move_uploaded_file($_FILES['documentos']['tmp_name'], $doc_path)) {
                $doc_db_path = 'uploads/documentos/' . $doc_name;
            } else {
                $error = 'Error al subir el nuevo documento.';
            }
        }

        if (empty($error)) {
            try {
                $stmt = $pdo->prepare(
                    "UPDATE formulario_matricula SET nombre = ?, programa = ?, nacionalidad = ?, foto = ?, email = ?, whatsapp = ?, documentos = ?, fecha_nacimiento = ? WHERE id_matricula = ?"
                );
                $stmt->execute([$nombre, $programa, $nacionalidad, $foto_db_path, $email, $whatsapp, $doc_db_path, $fecha_nacimiento, $matricula_id]);
                header('Location: index.php?success=' . urlencode('Solicitud actualizada con éxito.'));
                exit;
            } catch (PDOException $e) {
                $error = 'Error al actualizar la solicitud: ' . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/../partials/header.php';
?>

<div class="container">
    <h2><?php echo $page_title; ?></h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <form action="editar_matricula.php?id=<?php echo $matricula_id; ?>" method="POST" enctype="multipart/form-data" class="styled-form">
        <input type="hidden" name="current_foto_path" value="<?php echo htmlspecialchars($matricula['foto']); ?>">
        <input type="hidden" name="current_documentos_path" value="<?php echo htmlspecialchars($matricula['documentos']); ?>">

        <div class="form-group">
            <label for="nombre">Nombre Completo</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($matricula['nombre']); ?>" required>
        </div>

        <div class="form-group">
            <label for="programa">Programa de Interés</label>
            <select id="programa" name="programa" class="form-control" required>
                <?php
                $programas = ['Tecnico', 'Bachiller', 'Maestria', 'Doctorado'];
                foreach ($programas as $prog) {
                    $selected = ($matricula['programa'] === $prog) ? 'selected' : '';
                    echo "<option value='{$prog}' {$selected}>" . htmlspecialchars($prog) . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="nacionalidad">Nacionalidad</label>
            <select id="nacionalidad" name="nacionalidad" class="form-control" required>
                <?php
                $paises = ["Afganistán", "Albania", "Alemania", "Andorra", "Angola", "Antigua y Barbuda", "Arabia Saudita", "Argelia", "Argentina", "Armenia", "Australia", "Austria", "Azerbaiyán", "Bahamas", "Bangladés", "Barbados", "Baréin", "Bélgica", "Belice", "Benín", "Bielorrusia", "Birmania", "Bolivia", "Bosnia y Herzegovina", "Botsuana", "Brasil", "Brunéi", "Bulgaria", "Burkina Faso", "Burundi", "Bután", "Cabo Verde", "Camboya", "Camerún", "Canadá", "Catar", "Chad", "Chile", "China", "Chipre", "Ciudad del Vaticano", "Colombia", "Comoras", "Corea del Norte", "Corea del Sur", "Costa de Marfil", "Costa Rica", "Croacia", "Cuba", "Dinamarca", "Dominica", "Ecuador", "Egipto", "El Salvador", "Emiratos Árabes Unidos", "Eritrea", "Eslovaquia", "Eslovenia", "España", "Estados Unidos", "Estonia", "Etiopía", "Filipinas", "Finlandia", "Fiyi", "Francia", "Gabón", "Gambia", "Georgia", "Ghana", "Granada", "Grecia", "Guatemala", "Guyana", "Guinea", "Guinea ecuatorial", "Guinea-Bisáu", "Haití", "Honduras", "Hungría", "India", "Indonesia", "Irak", "Irán", "Irlanda", "Islandia", "Islas Marshall", "Islas Salomón", "Israel", "Italia", "Jamaica", "Japón", "Jordania", "Kazajistán", "Kenia", "Kirguistán", "Kiribati", "Kuwait", "Laos", "Lesoto", "Letonia", "Líbano", "Liberia", "Libia", "Liechtenstein", "Lituania", "Luxemburgo", "Macedonia del Norte", "Madagascar", "Malasia", "Malaui", "Maldivas", "Malí", "Malta", "Marruecos", "Mauricio", "Mauritania", "México", "Micronesia", "Moldavia", "Mónaco", "Mongolia", "Montenegro", "Mozambique", "Namibia", "Nauru", "Nepal", "Nicaragua", "Níger", "Nigeria", "Noruega", "Nueva Zelanda", "Omán", "Países Bajos", "Pakistán", "Palaos", "Panamá", "Papúa Nueva Guinea", "Paraguay", "Perú", "Polonia", "Portugal", "Reino Unido", "República Centroafricana", "República Checa", "República del Congo", "República Democrática del Congo", "República Dominicana", "Ruanda", "Rumanía", "Rusia", "Samoa", "San Cristóbal y Nieves", "San Marino", "San Vicente y las Granadinas", "Santa Lucía", "Santo Tomé y Príncipe", "Senegal", "Serbia", "Seychelles", "Sierra Leona", "Singapur", "Siria", "Somalia", "Sri Lanka", "Suazilandia", "Sudáfrica", "Sudán", "Sudán del Sur", "Suecia", "Suiza", "Surinam", "Tailandia", "Tanzania", "Tayikistán", "Timor Oriental", "Togo", "Tonga", "Trinidad y Tobago", "Túnez", "Turkmenistán", "Turquía", "Tuvalu", "Ucrania", "Uganda", "Uruguay", "Uzbekistán", "Vanuatu", "Venezuela", "Vietnam", "Yemen", "Yibuti", "Zambia", "Zimbabue"];
                foreach ($paises as $pais) {
                    $selected = ($matricula['nacionalidad'] === $pais) ? 'selected' : '';
                    echo "<option value='{$pais}' {$selected}>" . htmlspecialchars($pais) . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($matricula['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="whatsapp">Número de WhatsApp</label>
            <input type="text" id="whatsapp" name="whatsapp" class="form-control" value="<?php echo htmlspecialchars($matricula['whatsapp']); ?>" required>
        </div>

        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="<?php echo htmlspecialchars($matricula['fecha_nacimiento']); ?>" required>
        </div>

        <div class="form-group">
            <label for="foto">Cambiar Foto Tipo Carnet (JPG, PNG)</label>
            <?php if (!empty($matricula['foto'])): ?>
                <p>Foto actual: <a href="/learner/<?php echo htmlspecialchars($matricula['foto']); ?>" target="_blank">Ver Foto</a></p>
            <?php endif; ?>
            <input type="file" id="foto" name="foto" class="form-control" accept="image/jpeg, image/png">
        </div>

        <div class="form-group">
            <label for="documentos">Cambiar Documento de Identidad (PDF)</label>
            <?php if (!empty($matricula['documentos'])): ?>
                <p>Documento actual: <a href="/learner/<?php echo htmlspecialchars($matricula['documentos']); ?>" target="_blank">Ver PDF</a></p>
            <?php endif; ?>
            <input type="file" id="documentos" name="documentos" class="form-control" accept=".pdf">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Actualizar Solicitud</button>
            <a href="index.php" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>