
        <?php
require_once __DIR__ . '/../../classbox/config/database.php';

try {
    $stmt = $pdo->prepare("SELECT p.id_post, p.title, p.main_image, p.created_at FROM posts p JOIN categories c ON p.id_category = c.id_category WHERE c.name = ? ORDER BY p.created_at DESC LIMIT 5");
    $stmt->execute(['insumos']);
    $recent_posts = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error al cargar publicaciones recientes en lateral_index.php: " . $e->getMessage());
    $recent_posts = [];
}
?>

        <div class="col-lg-4 sidebar">

          <div class="widgets-container desktop-padding-left" data-aos="fade-up" data-aos-delay="200">


            <div class="recent-posts-widget widget-item">

              <h2 class="widget-title">Insumos de primera calidad</h2>

              <?php if (!empty($recent_posts)): ?>
                  <?php foreach ($recent_posts as $post): ?>
                  <div class="post-item">
                    <img src="<?php echo BASE_URL; ?>/public/uploads/images/<?php echo htmlspecialchars($post['main_image']); ?>" alt="" class="flex-shrink-0" style="box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);">
                    <div>
                      <h4><a href="/web_petrodrill/web_site/blog_details.php?id=<?php echo htmlspecialchars($post['id_post']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h4>
                      <time datetime="<?php echo htmlspecialchars($post['created_at']); ?>"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></time>
                    </div>
                  </div><!-- End recent post item-->
                  <?php endforeach; ?>
              <?php else: ?>
                  <p>No hay publicaciones recientes disponibles.</p>
              <?php endif; ?>

            </div><!--/Recent Posts Widget -->
            

            <div class="recent-posts-widget widget-item" style="margin-top: 15px;">

              <h2 class="widget-title">Clientes</h2>

              <?php
              try {
                  $stmt_clientes = $pdo->prepare("SELECT p.id_post, p.title, p.main_image, p.created_at FROM posts p JOIN categories c ON p.id_category = c.id_category WHERE c.name = ? ORDER BY p.created_at DESC LIMIT 5");
                  $stmt_clientes->execute(['clientes']);
                  $clientes_posts = $stmt_clientes->fetchAll();
              } catch (PDOException $e) {
                  error_log("Error al cargar publicaciones de clientes en lateral_index.php: " . $e->getMessage());
                  $clientes_posts = [];
              }
              ?>

              <?php if (!empty($clientes_posts)): ?>
                  <?php foreach ($clientes_posts as $post): ?>
                  <div class="post-item">
                    <img src="<?php echo BASE_URL; ?>/public/uploads/images/<?php echo htmlspecialchars($post['main_image']); ?>" alt="" class="flex-shrink-0" style="box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);">
                    <div>
                      <h4><a href="/web_petrodrill/web_site/blog_details.php?id=<?php echo htmlspecialchars($post['id_post']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h4>
                      <time datetime="<?php echo htmlspecialchars($post['created_at']); ?>"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></time>
                    </div>
                  </div><!-- End recent post item-->
                  <?php endforeach; ?>
              <?php else: ?>
                  <p>No hay publicaciones de clientes disponibles.</p>
              <?php endif; ?>

            </div><!--/Recent Posts Widget -->

          </div>

        </div>
