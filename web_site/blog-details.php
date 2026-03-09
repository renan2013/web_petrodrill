<?php
require_once __DIR__ . '/../classbox/config/database.php';

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
        echo "<h1>404 - Post Not Found</h1><p>Sorry, the post you are looking for does not exist.</p>";
        exit;
    }

    // Fetch attachments for this post (even if not used immediately, good to have)
    $att_stmt = $pdo->prepare("SELECT type, value FROM attachments WHERE id_post = ?");
    $att_stmt->execute([$post_id]);
    $attachments = $att_stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Error al cargar la publicación: " . $e->getMessage());
    echo "<h1>Error</h1><p>Hubo un problema al cargar la publicación.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'partials/head_section.php'; ?>
</head>

<body class="blog-details-page">

  <?php include 'partials/header_nav.php'; ?>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title">
      <div class="breadcrumbs">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php"><i class="bi bi-house"></i> <?php echo strtoupper('INICIO'); ?></a></li>
            <li class="breadcrumb-item"><a href="category.php?id=<?php echo htmlspecialchars($post['id_category']); ?>"><?php echo strtoupper(htmlspecialchars($post['category_name'])); ?></a></li>
            <li class="breadcrumb-item active current"><?php echo strtoupper('Detalle'); ?></li>
          </ol>
        </nav>
      </div>

      <div class="title-wrapper">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
      </div>
    </div><!-- End Page Title -->

    <div class="container">
      <div class="row">

        <div class="col-lg-8">

          <!-- Blog Details Section -->
          <section id="blog-details" class="blog-details section">
            <div class="container" data-aos="fade-up">

              <article class="article">

                <div class="hero-img" data-aos="zoom-in">
                  <img src="../classbox/<?php echo htmlspecialchars($post['main_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="img-fluid" loading="lazy">
                  <div class="meta-overlay">
                    <div class="meta-categories">
                      <a href="category.php?id=<?php echo htmlspecialchars($post['id_category']); ?>" class="category"><?php echo htmlspecialchars($post['category_name']); ?></a>
                     
                    </div>
                  </div>
                </div>

                <div class="article-content" data-aos="fade-up" data-aos-delay="100">
                  

                  <div class="content">
                    <?php
                    $post_content = $post['content'];
                    $post_content = str_replace('../../public/uploads/images/', '../classbox/public/uploads/images/', $post_content);
                    echo $post_content;
                    ?>
                  </div>
                    <hr/>
                     <span class="post-date"><i class="bi bi-calendar3"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                  </div>

               
                </div>

              </article>

            </div>
          </section><!-- /Blog Details Section -->

         

         


        </div>

        <div class="col-lg-4 sidebar">

          <div class="widgets-container" data-aos="fade-up" data-aos-delay="200">

            <p><?php echo ucfirst(htmlspecialchars($post['synopsis'])); ?></p>

            <!-- Attachments Widget -->
            <div class="recent-posts-widget widget-item">

              <h3 class="widget-title">Adjuntos</h3>

              <div class="post-item">
                <?php if (!empty($attachments)): ?>
                    <ul>
                        <?php foreach ($attachments as $att): ?>
                            <?php if ($att['type'] === 'pdf'): ?>
                              <hr/>
                                <li><a href="../classbox/<?php echo htmlspecialchars($att['value']); ?>" target="_blank">Descargar PDF</a></li>
                            <?php elseif ($att['type'] === 'youtube'): ?>
                                <li>
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
              </div><!-- End attachments item-->

            </div><!--/Attachments Widget -->

    

          </div>

        </div>

      </div>
    </div>

  </main>

  <?php include 'partials/footer_scripts.php'; ?>

</body>

</html>