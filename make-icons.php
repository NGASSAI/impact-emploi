<?php
/**
 * Generate Proper PWA Icons - Briefcase/Job themed icons
 * Creates real PNG icons that represent an employment application
 */

// Create a minimal valid PNG with proper structure
function createBriefcaseIcon($size) {
    // For simplicity, create a solid colored icon with the app's brand colors
    // In production, you'd use GD library to draw a briefcase icon
    
    // PNG file structure
    $width = $size;
    $height = $size;
    
    // Create image data (simple solid color with alpha channel)
    $pngData = '';
    
    // PNG Signature
    $pngData .= pack('C*', 0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A);
    
    // IHDR chunk
    $ihdrData = pack('N', $width);  // Width
    $ihdrData .= pack('N', $height); // Height
    $ihdrData .= pack('C', 8);       // Bit depth
    $ihdrData .= pack('C', 6);       // Color type (RGBA)
    $ihdrData .= pack('C', 0);       // Compression
    $ihdrData .= pack('C', 0);       // Filter
    $ihdrData .= pack('C', 0);       // Interlace
    
    $pngData .= createChunk('IHDR', $ihdrData);
    
    // Create raw image data (RGBA)
    $rawData = '';
    $primary = [0, 82, 163];    // #0052A3 - main blue
    $white = [255, 255, 255];
    
    for ($y = 0; $y < $height; $y++) {
        $rawData .= "\x00"; // Filter byte (none)
        for ($x = 0; $x < $width; $x++) {
            // Create a briefcase-like pattern
            $inBriefcase = false;
            $cx = $x / $size;
            $cy = $y / $size;
            
            // Briefcase body (rectangle in middle)
            if ($cx >= 0.2 && $cx <= 0.8 && $cy >= 0.35 && $cy <= 0.8) {
                $inBriefcase = true;
            }
            
            // Briefcase handle (small rectangle at top)
            if ($cx >= 0.4 && $cx <= 0.6 && $cy >= 0.25 && $cy <= 0.35) {
                $inBriefcase = true;
            }
            
            // Briefcase clasp (small rectangle in middle top)
            if ($cx >= 0.45 && $cx <= 0.55 && $cy >= 0.35 && $cy <= 0.45) {
                $inBriefcase = true;
            }
            
            if ($inBriefcase) {
                $rawData .= pack('C*', $white[0], $white[1], $white[2], 255);
            } else {
                // Transparent background
                $rawData .= pack('C*', $primary[0], $primary[1], $primary[2], 255);
            }
        }
    }
    
    // Compress with zlib
    $compressed = gzcompress($rawData, 9);
    $pngData .= createChunk('IDAT', $compressed);
    
    // IEND chunk
    $pngData .= createChunk('IEND', '');
    
    return $pngData;
}

function createChunk($type, $data) {
    $length = pack('N', strlen($data));
    $crc = crc32($type . $data);
    $crc = pack('N', $crc & 0xffffffff);
    return $length . $type . $data . $crc;
}

// Generate icons
$outputDir = __DIR__ . '/assets/img';

// 192x192 icon
$icon192 = createBriefcaseIcon(192);
file_put_contents($outputDir . '/icon-192.png', $icon192);
echo "Created: icon-192.png (192x192)\n";

// 512x512 icon
$icon512 = createBriefcaseIcon(512);
file_put_contents($outputDir . '/icon-512.png', $icon512);
echo "Created: icon-512.png (512x512)\n";

echo "\n✅ PWA Briefcase Icons Generated!\n";
?>

