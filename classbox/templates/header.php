<?php
require_once __DIR__ . '/../config/database.php';

// Function to construct full URLs for display, handling potential issues
function get_full_url($relative_url) {
    echo "<!-- DEBUG get_full_url: Initial relative_url: " . htmlspecialchars($relative_url) . " -->\n";

    // If it's already a full external URL, return as is.
    if (preg_match('/^(http|https):\/\//i', $relative_url)) {
        echo "<!-- DEBUG get_full_url: External URL detected. Returning: " . htmlspecialchars($relative_url) . " -->\n";
        return $relative_url;
    }

    // Explicitly remove "web_site/" prefix from the URL.
    // This is done early to ensure it's gone before other processing.
    $relative_url_after_web_site_removal = str_replace('web_site/', '', $relative_url);
    echo "<!-- DEBUG get_full_url: After web_site removal: " . htmlspecialchars($relative_url_after_web_site_removal) . " -->\n";

    // Get the base URL for the classbox application (e.g., https://petrodrillperu.com/classbox)
    $base_app_url = rtrim(BASE_URL, '/');
    echo "<!-- DEBUG get_full_url: base_app_url: " . htmlspecialchars($base_app_url) . " -->\n";

    // Get the root domain URL (e.g., https://petrodrillperu.com)
    $parsed_base_app_url = parse_url($base_app_url);
    $root_domain_url = $parsed_base_app_url['scheme'] . '://' . $parsed_base_app_url['host'];
    echo "<!-- DEBUG get_full_url: root_domain_url: " . htmlspecialchars($root_domain_url) . " -->\n";

    // Use the URL after web_site removal for further processing
    $current_relative_url = $relative_url_after_web_site_removal;

    // If the current_relative_url starts with a slash, it's relative to the root domain.
    if (substr($current_relative_url, 0, 1) === '/') {
        // Ensure only one leading slash for root-relative URLs
        $final_url = $root_domain_url . '/' . ltrim($current_relative_url, '/');
        echo "<!-- DEBUG get_full_url: Root-relative path. Final URL: " . htmlspecialchars($final_url) . " -->\n";
        return $final_url;
    } else {
        // It's relative to the classbox application base URL.
        // Ensure no leading slash on current_relative_url for clean concatenation.
        $final_url = $base_app_url . '/' . ltrim($current_relative_url, '/');
        echo "<!-- DEBUG get_full_url: App-relative path. Final URL: " . htmlspecialchars($final_url) . " -->\n";
        return $final_url;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Classbox Site'; ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 0; background-color: #fff; color: #333; }
        .container { max-width: 1100px; margin: 0 auto; padding: 20px; }
        .main-nav { background-color: #1f2937; padding: 15px 0; }
        .nav-container { max-width: 1100px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; }
        .main-nav ul { list-style-type: none; padding: 0; margin: 0; }
        .main-nav li { display: inline-block; margin: 0 15px; }
        .main-nav a { color: #fff; text-decoration: none; font-weight: 500; font-size: 16px; }
        .search-form input { padding: 8px; border: 1px solid #4b5563; border-radius: 4px 0 0 4px; background-color: #374151; color: #fff; }
        .search-form button { padding: 8px 12px; border: none; background-color: #007bff; color: #fff; cursor: pointer; border-radius: 0 4px 4px 0; margin-left: -5px; }
        .footer { background-color: #f8f9fa; text-align: center; padding: 20px; margin-top: 40px; border-top: 1px solid #e9ecef; }
    </style>
</head>
<body>

<nav class="main-nav">
    <div class="nav-container">
            <ul>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'superadmin'): ?>
                    <li><a href="<?php echo get_full_url('/admin/manage_permissions.php'); ?>">Gestionar Permisos</a></li>
                <?php endif; ?>
                <?php
                try {
                    $menu_stmt = $pdo->query("SELECT title, url FROM menus ORDER BY display_order ASC");
                    while ($row = $menu_stmt->fetch()) {
                        $normalized_url_from_db = $row['url'];
                        $final_display_url = get_full_url($normalized_url_from_db);
                        echo '<!-- DEBUG: DB URL: ' . htmlspecialchars($normalized_url_from_db) . ' | Final URL: ' . htmlspecialchars($final_display_url) . ' -->';
                        echo '<li><a href="' . htmlspecialchars($final_display_url) . '">' . htmlspecialchars($row['title']) . '</a></li>';
                    }
                } catch (PDOException $e) {
                    // Fail silently on public site
                }
                ?>
            </ul>
            <form action="<?php echo get_full_url('/search.php'); ?>" method="GET" class="search-form">
                <input type="search" name="query" placeholder="Search posts..." required>
                <button type="submit">Search</button>
            </form>
        </div>
</nav>

<main class="container">
