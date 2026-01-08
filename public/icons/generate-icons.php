<?php
/**
 * PWA Icon Generator
 * Run this script once to generate all required icon sizes
 * Usage: php generate-icons.php
 */

$sizes = [72, 96, 128, 144, 152, 192, 384, 512];
$sourceImage = __DIR__ . '/../fav.png';
$outputDir = __DIR__;

if (!file_exists($sourceImage)) {
    // Create a simple placeholder if source doesn't exist
    foreach ($sizes as $size) {
        createPlaceholderIcon($outputDir . "/icon-{$size}x{$size}.png", $size);
    }
    echo "Created placeholder icons (no source image found)\n";
    exit;
}

// Check if GD is available
if (!extension_loaded('gd')) {
    echo "GD extension not available. Creating SVG placeholders instead.\n";
    foreach ($sizes as $size) {
        createPlaceholderIcon($outputDir . "/icon-{$size}x{$size}.png", $size);
    }
    exit;
}

// Load source image
$source = @imagecreatefrompng($sourceImage);
if (!$source) {
    $source = @imagecreatefromjpeg($sourceImage);
}
if (!$source) {
    echo "Could not load source image. Creating placeholders.\n";
    foreach ($sizes as $size) {
        createPlaceholderIcon($outputDir . "/icon-{$size}x{$size}.png", $size);
    }
    exit;
}

$sourceWidth = imagesx($source);
$sourceHeight = imagesy($source);

foreach ($sizes as $size) {
    $dest = imagecreatetruecolor($size, $size);
    
    // Preserve transparency
    imagealphablending($dest, false);
    imagesavealpha($dest, true);
    $transparent = imagecolorallocatealpha($dest, 0, 0, 0, 127);
    imagefill($dest, 0, 0, $transparent);
    imagealphablending($dest, true);
    
    // Resize
    imagecopyresampled($dest, $source, 0, 0, 0, 0, $size, $size, $sourceWidth, $sourceHeight);
    
    // Save
    $outputPath = $outputDir . "/icon-{$size}x{$size}.png";
    imagepng($dest, $outputPath);
    imagedestroy($dest);
    
    echo "Created: icon-{$size}x{$size}.png\n";
}

imagedestroy($source);
echo "All icons generated successfully!\n";

function createPlaceholderIcon($path, $size) {
    // Create a simple colored square as placeholder
    $img = imagecreatetruecolor($size, $size);
    $bgColor = imagecolorallocate($img, 7, 94, 84); // #075e54
    imagefill($img, 0, 0, $bgColor);
    
    // Add text
    $textColor = imagecolorallocate($img, 255, 255, 255);
    $text = "KTK";
    $fontSize = max(1, (int)($size / 8));
    
    // Center the text
    $textWidth = imagefontwidth($fontSize) * strlen($text);
    $textHeight = imagefontheight($fontSize);
    $x = ($size - $textWidth) / 2;
    $y = ($size - $textHeight) / 2;
    
    imagestring($img, $fontSize, $x, $y, $text, $textColor);
    
    imagepng($img, $path);
    imagedestroy($img);
}

