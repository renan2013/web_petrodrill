<?php
require_once 'config/database.php';

$post_id = $_GET['id'] ?? null;
if (!$post_id) {
    header('Location: index.php');
    exit;
}

// Fetch post data along with its category
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
    $page_title = 'Not Found';
    require_once 'templates/header.php';
    echo "<h1>404 - Post Not Found</h1><p>Sorry, the post you are looking for does not exist.</p>";
    require_once 'templates/footer.php';
    exit;
}

$page_title = htmlspecialchars($post['title']);

// Fetch attachments for this post
$att_stmt = $pdo->prepare("SELECT type, value FROM attachments WHERE id_post = ?");
$att_stmt->execute([$post_id]);
$attachments = $att_stmt->fetchAll();

require_once 'templates/header.php';
?>

<div class="post-container">
    <article>
        <header class="post-header">
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                        <p class="post-meta">
                Category: <a href="category.php?id=<?php echo $post['id_category']; ?>"><?php echo htmlspecialchars($post['category_name']); ?></a> 
                | Published on: <?php echo date('F j, Y', strtotime($post['created_at'])); ?></p>
        </header>

        <?php 
        // --- Conditional Rendering Based on Category ---
        
        // If it's a gallery post
        if (stripos($post['category_name'], 'gallery') !== false): 
        ?>
            <div class="gallery-grid">
                <?php foreach ($attachments as $att): ?>
                    <?php if ($att['type'] === 'gallery_image'): ?>
                        <div class="gallery-item">
                            <a href="/classbox/<?php echo htmlspecialchars($att['value']); ?>" target="_blank">
                                <img src="/classbox/<?php echo htmlspecialchars($att['value']); ?>" alt="Gallery Image">
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

        <?php 
        // For all other standard posts
        else: 
        ?>
            <?php if (!empty($post['main_image'])): ?>
                <div class="post-main-image">
                    <img src="/classbox/<?php echo htmlspecialchars($post['main_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                </div>
            <?php endif; ?>

            <div class="post-content">
                <?php echo $post['content']; // Directly outputting HTML from the WYSIWYG editor ?>
            </div>
        <?php endif; ?>

        <!-- Attachments Section (for non-gallery posts) -->
        <?php if (stripos($post['category_name'], 'gallery') === false && !empty($attachments)): ?>
            <div class="attachments-section">
                <h3>Attachments</h3>
                <ul>
                    <?php foreach ($attachments as $att): ?>
                        <?php if ($att['type'] === 'pdf'): ?>
                            <li><a href="/classbox/<?php echo htmlspecialchars($att['value']); ?>" target="_blank">Download PDF</a></li>
                        <?php elseif ($att['type'] === 'youtube'): ?>
                            <div class="youtube-video">
                                <?php
                                // Extract YouTube ID from URL
                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|v\/|e(?:mbed)?\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $att['value'], $match);
                                $youtube_id = $match[1] ?? '';
                                if ($youtube_id) {
                                    echo '<iframe src="https://www.youtube.com/embed/' . $youtube_id . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

    </article>
</div>

<style>
.post-container { max-width: 800px; margin: 0 auto; }
.post-header { border-bottom: 1px solid #eee; margin-bottom: 30px; padding-bottom: 20px; }
.post-header h1 { font-size: 36px; margin: 0; }
.post-meta { color: #777; font-size: 0.9em; }
.post-main-image img { max-width: 100%; height: auto; border-radius: 8px; margin-bottom: 30px; }
.post-content { line-height: 1.7; font-size: 16px; }
.attachments-section { margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; }
.attachments-section h3 { margin-bottom: 15px; }
.attachments-section ul { list-style-type: none; padding: 0; }
.attachments-section li { margin-bottom: 10px; }
.youtube-video { position: relative; padding-bottom: 56.25%; /* 16:9 */ height: 0; margin-top: 20px; }
.youtube-video iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
.gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
.gallery-item img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; transition: transform 0.2s; }
.gallery-item img:hover { transform: scale(1.05); }
</style>

<?php
require_once 'templates/footer.php';
?>