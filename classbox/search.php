<?php
require_once 'config/database.php';

$search_query = $_GET['query'] ?? '';

if (empty($search_query)) {
    header('Location: index.php');
    exit;
}

$page_title = 'Search Results for: ' . htmlspecialchars($search_query);

// Perform the search
$posts_stmt = $pdo->prepare("
    SELECT id_post, title, synopsis, main_image
    FROM posts
    WHERE title LIKE :query OR content LIKE :query
    ORDER BY created_at DESC
");
$posts_stmt->execute([':query' => '%' . $search_query . '%']);
$posts = $posts_stmt->fetchAll();

require_once 'templates/header.php';
?>

<div class="search-header">
    <h1>Search Results</h1>
    <p>Found <?php echo count($posts); ?> results for: <strong>"<?php echo htmlspecialchars($search_query); ?>"</strong></p>
</div>

<!-- Posts Grid Section -->
<div class="posts-grid">
    <?php if (empty($posts)): ?>
        <p>No posts matched your search criteria. Please try a different keyword.</p>
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
.search-header { text-align: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
.search-header h1 { font-size: 32px; }
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