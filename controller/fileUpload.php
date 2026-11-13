<?php
function handleImageUpload($fileInput)
{
    if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $file = $_FILES[$fileInput];
    if ($file['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('The image upload did not complete. Please try again.');
    if ($file['size'] > 2 * 1024 * 1024) throw new RuntimeException('Choose an image smaller than 2 MB.');

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime])) throw new RuntimeException('Use a JPG, PNG, GIF or WEBP image.');

    $filename = 'place_' . bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
    $destination = __DIR__ . '/../asset/uploads/posts/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) throw new RuntimeException('The image could not be saved. Please try again.');
    return 'asset/uploads/posts/' . $filename;
}
