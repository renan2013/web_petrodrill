<?php
require_once __DIR__ . '/../../classbox/config/database.php';

try {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM posts p JOIN categories c ON p.id_category = c.id_category WHERE c.name NOT IN (?, ?) ORDER BY c.name ASC, p.created_at DESC");
    $stmt->execute(['insumos', 'clientes']);
    $posts = $stmt->fetchAll();

    $groupedPosts = [];
    foreach ($posts as $post) {
        $groupedPosts[$post['category_name']][] = $post;
    }

} catch (PDOException $e) {
    error_log("Error al cargar las publicaciones: " . $e->getMessage());
    $posts = []; // Asegura que $posts sea un array vacío en caso de error
    $groupedPosts = [];
}
?>
    <!-- Page Title -->
    <div class="page-title position-relative" >
      

      <div class="title-wrapper">
        <h1>Nuestros Productos</h2>
        
      </div>
    </div><!-- End Page Title -->

    <div class="container" style="margin-top: -50px;">
      <div class="row">

        <div class="col-lg-8">

          <!-- Category Posts Section -->
          <section id="category-posts" class="category-posts section">

            <div class="container" data-aos="fade-up" data-aos-delay="100">
              <?php foreach ($groupedPosts as $categoryName => $categoryPosts): ?>
             
              <div class="row gy-4 mb-5">

                <?php foreach ($categoryPosts as $post): ?>
                <div class="col-lg-6">
                  <article>

                    <div class="post-img">
                      <img src="<?php echo BASE_URL; ?>/public/uploads/images/<?php echo htmlspecialchars($post['main_image']); ?>" alt="" class="img-fluid">
                    </div>

                    <p class="post-category">Categoría: <?php echo htmlspecialchars($post['category_name']); ?></p>

                    <h2 class="title">
                      <a href="web_site/blog_details.php?id=<?php echo htmlspecialchars($post['id_post']); ?>"><?php echo htmlspecialchars($post['title']); ?></a>
                    </h2>
                    <p class="post-synopsis"><?php echo htmlspecialchars($post['synopsis']); ?></p>

                    <div class="d-flex align-items-center">
                      <!-- Assuming author info is not in posts table, using static for now -->
                      
                      <div class="post-meta">
                        
                        <p class="post-date">
                          <?php echo date('M d, Y', strtotime($post['created_at'])); ?></time>
                        </p>
                      </div>
                    </div>

                  </article>
                </div><!-- End post list item -->
                <?php endforeach; ?>

              </div>
              <?php endforeach; ?>
            </div>

          </section><!-- /Category Posts Section -->

         

        </div>

        
          <?php include 'lateral_index.php'; ?>
        

      </div>