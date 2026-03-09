<?php
$page_title = 'Welcome';
require_once 'templates/header.php';

// Fetch slider images
$slider_stmt = $pdo->query("
    SELECT a.value as image_path, p.id_post, p.title
    FROM attachments a
    JOIN posts p ON a.id_post = p.id_post
    WHERE a.type = 'slider_image'
    ORDER BY p.created_at DESC
");
$slider_images = $slider_stmt->fetchAll();

// Fetch recent posts (excluding special categories like 'Gallery')
$posts_stmt = $pdo->query("
    SELECT p.id_post, p.title, p.synopsis, p.main_image, c.name as category_name
    FROM posts p
    JOIN categories c ON p.id_category = c.id_category
    WHERE LOWER(c.name) NOT LIKE '%gallery%'
    ORDER BY p.created_at DESC
    LIMIT 9
");
$posts = $posts_stmt->fetchAll();
?>

<!-- Slider Section -->
<?php if (!empty($slider_images)): ?>
<div class="slider">
    <!-- Basic slider implementation, can be replaced with a JS library -->
    <div class="slide-container">
        <?php foreach ($slider_images as $index => $slide): ?>
            <div class="slide fade">
                <a href="post.php?id=<?php echo $slide['id_post']; ?>">
                    <img src="<?php echo htmlspecialchars($slide['image_path']); ?>" alt="<?php echo htmlspecialchars($slide['title']); ?>">
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Posts Grid Section -->
<div class="posts-grid">
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
</div>

<style>
/* Slider Styles */
.slider { max-width: 100%; margin-bottom: 40px; }
.slide-container { position: relative; }
.slide { display: none; }
.slide img { width: 100%; height: auto; max-height: 400px; object-fit: cover; border-radius: 8px; }
.fade { animation: fade 1.5s; }
@keyframes fade { from {opacity: .4} to {opacity: 1} }

/* Posts Grid Styles */
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

<script>
// Simple JS for automatic slider
let slideIndex = 0;
showSlides();
function showSlides() {
    let slides = document.getElementsByClassName("slide");
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";  
    }
    slideIndex++;
    if (slideIndex > slides.length) {slideIndex = 1}
    if (slides.length > 0) {
        slides[slideIndex-1].style.display = "block";  
        setTimeout(showSlides, 4000); // Change image every 4 seconds
    }
}
</script>

<?php
require_once 'templates/footer.php';
?>