<?php
require_once 'config/database.php';

$category_id = $_GET['id'] ?? null;
if (!$category_id) {
    header('Location: index.php');
    exit;
}

// Fetch category details
$cat_stmt = $pdo->prepare("SELECT name FROM categories WHERE id_category = ?");
$cat_stmt->execute([$category_id]);
$category = $cat_stmt->fetch();

if (!$category) {
    http_response_code(404);
    $page_title = 'Not Found';
    require_once 'templates/header.php';
    echo "<h1>404 - Category Not Found</h1><p>Sorry, the category you are looking for does not exist.</p>";
    require_once 'templates/footer.php';
    exit;
}

$page_title = 'Category: ' . htmlspecialchars($category['name']);

// Fetch all posts in this category
$posts_stmt = $pdo->prepare("
    SELECT p.id_post, p.title, p.synopsis, p.main_image
    FROM posts p
    WHERE p.id_category = ?
    ORDER BY p.created_at DESC
");
$posts_stmt->execute([$category_id]);
$posts = $posts_stmt->fetchAll();

require_once 'templates/header.php';
?>

<div class="category-header">
    <h1><?php echo htmlspecialchars($category['name']); ?></h1>
</div>

<!-- Posts Grid Section -->
<div class="posts-grid">
    <?php if (empty($posts)): ?>
        <p>There are no posts in this category yet.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-card">
                <a href="post.php?id=<?php echo $post['id_post']; ?>">
                    <?php if (!empty($post['main_image'])): ?>
                        <img src="<?php echo htmlspecialchars($post['main_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="post-card-img">
                    <?php endif; ?>
                    <div class="post-card-content">
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p><?php echo htmlspecialchars($post['synopsis']); ?></p>
                        <span class="read-more">Read More &rarr;</span>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.category-header { text-align: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
.category-header h1 { font-size: 32px; }
/* Re-using styles from index.php for consistency */
.posts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
.post-card { background-color: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); overflow: hidden; text-decoration: none; color: inherit; transition: transform 0.2s; }
.post-card:hover { transform: translateY(-5px); }
.post-card a { text-decoration: none; color: inherit; display: block; }
.post-card-img { width: 100%; height: 200px; object-fit: cover; }
.post-card-content { padding: 20px; }
.post-card-content h3 { margin: 0 0 10px 0; }
.post-card-content p { margin: 0 0 15px 0; color: #555; }
.read-more { font-weight: 600; color: #007bff; }
</style>

<?php
require_once 'templates/footer.php';
?>