<?php
$slider_stmt = $pdo->query("
    SELECT a.value as image_path, p.id_post, p.title
    FROM attachments a
    JOIN posts p ON a.id_post = p.id_post
    WHERE a.type = 'slider_image'
    ORDER BY p.created_at DESC
");
$slider_images = $slider_stmt->fetchAll();
?>

<?php if (!empty($slider_images)): ?>
<div class="slideshow-container">

    <?php foreach ($slider_images as $index => $slide): ?>
    <div class="mySlides fade">
        <a href="/web_site/blog_details.php?id=<?php echo $slide['id_post']; ?>">
            <img src="<?php echo BASE_URL; ?>/public/<?php echo htmlspecialchars($slide['image_path']); ?>" alt="<?php echo htmlspecialchars($slide['title']); ?>">
        </a>
    </div>
    <?php endforeach; ?>

    <!-- Next and previous buttons -->
<!--     <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
    <a class="next" onclick="plusSlides(1)">&#10095;</a> -->

    <!-- The dots/circles -->
    <div style="text-align:center">
        <?php foreach ($slider_images as $index => $slide): ?>
        <span class="dot" onclick="currentSlide(<?php echo $index + 1; ?>)"></span>
        <?php endforeach; ?>
    </div>

</div>

<style>
/* Slideshow container */
.slideshow-container {
  max-width: 100%;
  position: relative;
  margin: auto;
  overflow: hidden;
}

/* Hide the images by default */
.mySlides {
  opacity: 0;
  visibility: hidden;
  position: absolute;
  width: 100%;
  transition: opacity 1.5s ease-in-out, visibility 1.5s ease-in-out;
}

.mySlides.active {
  opacity: 1;
  visibility: visible;
  position: relative; /* To take up space */
}

.mySlides img {
    width: 100%;
    display: block;
    height: auto;
    object-fit: cover;
}

/* Next & previous buttons */
.prev, .next {
  cursor: pointer;
  position: absolute;
  top: 50%;
  width: auto;
  padding: 16px;
  margin-top: -22px;
  color: white;
  font-weight: bold;
  font-size: 18px;
  transition: 0.6s ease;
  border-radius: 0 3px 3px 0;
  user-select: none;
  background-color: rgba(0,0,0,0.8);
}

/* Position the "next button" to the right */
.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

/* On hover, add a black background with a little opacity */
.prev:hover, .next:hover {
  background-color: rgba(0,0,0,0.8);
}

/* The dots/bullets/indicators */
.dot {
  cursor: pointer;
  height: 15px;
  width: 15px;
  margin: 0 2px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
  transition: background-color 0.6s ease;
}

.active, .dot:hover {
  background-color: #717171;
}

/* Fading animation */
.fade {
  animation-name: fade;
  animation-duration: 1.5s;
}

@keyframes fade {
  from {opacity: .4}
  to {opacity: 1}
}
</style>

<script>
let slideIndex = 0; // Start from 0 for easier array indexing
let slides = document.getElementsByClassName("mySlides");
let dots = document.getElementsByClassName("dot");
let slideInterval; // Variable to hold the interval

// Initial call to show the first slide and start auto-play
showSlides();

function plusSlides(n) {
  clearTimeout(slideInterval); // Clear auto-play when manually navigating
  slideIndex += n;
  showSlides();
}

function currentSlide(n) {
  clearTimeout(slideInterval); // Clear auto-play when manually navigating
  slideIndex = n - 1; // Adjust for 0-based index
  showSlides();
}

function showSlides() {
  let i;
  
  if (slides.length === 0) return; // No slides to show

  // Reset index if it goes out of bounds
  if (slideIndex >= slides.length) {slideIndex = 0}
  if (slideIndex < 0) {slideIndex = slides.length - 1}

  // Hide all slides and remove active class from dots
  for (i = 0; i < slides.length; i++) {
    slides[i].classList.remove("active");
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }

  // Show the current slide and add active class to the current dot
  slides[slideIndex].classList.add("active");
  dots[slideIndex].className += " active";

  // Auto-advance after 3 seconds (3000 milliseconds)
  slideInterval = setTimeout(function() {
    slideIndex++;
    showSlides();
  }, 3000);
}
</script>
<?php endif; ?>