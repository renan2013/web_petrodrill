
<?php
// Incluir conexión a la base de datos y configuración
require_once __DIR__ . '/../classbox/config/database.php';

include 'partials/head_section.php';
include 'partials/header_nav.php';

$post_id = $_GET['id'] ?? null;

if (!$post_id) {
    header('Location: ../index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name
        FROM posts p
        JOIN categories c ON p.id_category = c.id_category
        WHERE p.id_post = ?
    ");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if (!$post) {
        http_response_code(404);
        echo "<div class='container mt-5'><h1>404 - Post Not Found</h1><p>Sorry, the post you are looking for does not exist.</p></div>";
        include 'partials/footer.php';
        include 'partials/footer_scripts.php';
        exit;
    }

    // Fetch attachments for this post
    $att_stmt = $pdo->prepare("SELECT type, value FROM attachments WHERE id_post = ?");
    $att_stmt->execute([$post_id]);
    $attachments = $att_stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Error al cargar la publicación: " . $e->getMessage());
    echo "<div class='container mt-5'><h1>Error</h1><p>Hubo un problema al cargar la publicación.</p></div>";
    include 'partials/footer.php';
    include 'partials/footer_scripts.php';
    exit;
}
?>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title">
      <div class="title-wrapper">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
      </div>
    </div><!-- End Page Title -->

    <div class="container">
      <div class="row">

        <div class="col-12 col-lg-8">

          <!-- Blog Details Section -->
          <section id="blog-details" class="blog-details section">
            <div class="container" data-aos="fade-up">

              <article class="article">

                <div class="hero-img" data-aos="zoom-in">
                  <?php if (!empty($post['main_image'])): ?>
                    <img src="<?php echo BASE_URL; ?>/public/uploads/images/<?php echo htmlspecialchars($post['main_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="img-fluid" loading="lazy">
                  <?php endif; ?>
                </div>

                <div class="article-content" data-aos="fade-up" data-aos-delay="100">
                  <div class="content">
                    <?php
                    $post_content = $post['content'];
                    // Ajustar rutas de imágenes internas del editor
                    $post_content = str_replace('../../public/uploads/images/', BASE_URL . '/public/uploads/images/', $post_content);
                    echo $post_content;
                    ?>
                  </div>
                </div>

              </article>

            </div>
          </section><!-- /Blog Details Section -->
        </div>

        <div class="col-12 col-lg-4 sidebar" >
          <div class="widget-item">
            <h3 class="widget-title">Resumen</h3>
            <p><?php echo ucfirst(htmlspecialchars($post['synopsis'])); ?></p>
          </div>
          
          <!-- Attachments Widget -->
          <div class="categories-widget widget-item">
            <h3 class="widget-title">Adjuntos</h3>
            <div class="post-item">
              <?php if (!empty($attachments)): ?>
                  <ul class="list-unstyled">
                      <?php foreach ($attachments as $att): ?>
                          <?php if ($att['type'] === 'pdf'): ?>
                              <li class="mb-2"><a href="<?php echo BASE_URL; ?>/public/uploads/attachments/<?php echo htmlspecialchars($att['value']); ?>" target="_blank" class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-file-pdf"></i> Descargar PDF</a></li>
                          <?php elseif ($att['type'] === 'youtube'): ?>
                              <li class="mb-3">
                                  <?php
                                  preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|v\/|e(?:mbed)?\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $att['value'], $match);
                                  $youtube_id = $match[1] ?? '';
                                  if ($youtube_id) {
                                      echo '<div class="youtube-video"><iframe src="https://www.youtube.com/embed/' . $youtube_id . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
                                  }
                                  ?>
                              </li>
                          <?php endif; ?>
                      <?php endforeach; ?>
                  </ul>
              <?php else: ?>
                  <p>No hay adjuntos para esta publicación.</p>
              <?php endif; ?>
            </div>
          </div><!--/Attachments Widget -->
        </div>

      </div>
    </div>

  </main>

<style>
.youtube-video {
    position: relative;
    padding-bottom: 56.25%; 
    height: 0;
    overflow: hidden;
    max-width: 100%;
    background: #000;
    margin-bottom: 15px;
}
.youtube-video iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
.hero-img img {
    width: 100%;
    border-radius: 8px;
    margin-bottom: 20px;
}
</style>

<?php 
include 'partials/footer.php';
include 'partials/footer_scripts.php'; 
?>