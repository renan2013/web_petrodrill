<?php
session_start();
require_once __DIR__ . '/../../auth/check_auth.php'; // Ensure user is logged in
require_once __DIR__ . '/../../config/config.php';   // Include BASE_URL

// This is a dedicated handler for image uploads from TinyMCE.

/*********************************************
 * The upload directory.
 * Must be writable by the web server.
 *********************************************/
$image_folder = __DIR__ . "/../../public/uploads/images/";

// Sanitize the file name
if (empty($_FILES['file']['name'])) {
    header("HTTP/1.1 500 Server Error"); // File name is missing
    return;
}
$temp = explode(".", $_FILES["file"]["name"]);
$sanitized_name = preg_replace('/[^a-zA-Z0-9-_.]/', '', basename($_FILES["file"]["name"]));
$new_filename = round(microtime(true)) . '-' . $sanitized_name;

// Check if the file is a valid image
$mime_types = ['image/gif', 'image/jpeg', 'image/png'];
if (!in_array($_FILES['file']['type'], $mime_types)) {
    header("HTTP/1.1 400 Invalid file type.");
    return;
}

$file_path = $image_folder . $new_filename;

// --- Image Optimization using GD Library ---
$max_width = 1200; // Maximum width for uploaded images

$image_info = getimagesize($_FILES['file']['tmp_name']);
$original_width = $image_info[0];
$original_height = $image_info[1];
$mime = $image_info['mime'];

$source_image = null;
switch ($mime) {
    case 'image/jpeg':
        $source_image = imagecreatefromjpeg($_FILES['file']['tmp_name']);
        break;
    case 'image/png':
        $source_image = imagecreatefrompng($_FILES['file']['tmp_name']);
        break;
    case 'image/gif':
        $source_image = imagecreatefromgif($_FILES['file']['tmp_name']);
        break;
}

if ($source_image) {
    $new_width = $original_width;
    $new_height = $original_height;

    // Resize if image is wider than max_width
    if ($original_width > $max_width) {
        $new_width = $max_width;
        $new_height = ($original_height / $original_width) * $new_width;
    }

    $resized_image = imagecreatetruecolor($new_width, $new_height);

    // Preserve transparency for PNG and GIF
    if ($mime == 'image/png') {
        imagealphablending($resized_image, false);
        imagesavealpha($resized_image, true);
    } elseif ($mime == 'image/gif') {
        $transparent_index = imagecolortransparent($source_image);
        if ($transparent_index >= 0) {
            $transparent_color = imagecolorsforindex($source_image, $transparent_index);
            $transparent_new_color = imagecolorallocate($resized_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
            imagefill($resized_image, 0, 0, $transparent_new_color);
            imagecolortransparent($resized_image, $transparent_new_color);
        }
    }

    imagecopyresampled($resized_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);

    $success = false;
    switch ($mime) {
        case 'image/jpeg':
            $success = imagejpeg($resized_image, $file_path, 80); // 80% quality
            break;
        case 'image/png':
            $success = imagepng($resized_image, $file_path, 7); // Compression level 0-9 (9 is max compression)
            break;
        case 'image/gif':
            $success = imagegif($resized_image, $file_path);
            break;
    }

    imagedestroy($source_image);
    imagedestroy($resized_image);

    if ($success) {
        // Respond with the JSON object that TinyMCE expects
        // Construct the public URL using the BASE_URL from the config
        $public_url = '/classbox/public/uploads/images/' . $new_filename;
        header('Content-Type: application/json');
        echo json_encode(array('location' => $public_url));
    } else {
        header("HTTP/1.1 500 Server Error"); // Failed to save optimized image
    }
} else {
    header("HTTP/1.1 500 Server Error"); // Failed to create image resource
}
?>