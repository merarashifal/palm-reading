<?php

namespace AIAnalysisEngine\Renderer;

use AIAnalysisEngine\Presentation\DTO\ReportDocument;

class SocialCardRenderer
{
    /**
     * Generates a square, story, and landscape image using GD.
     */
    public function renderToFiles(ReportDocument $document, string $outputDir): void
    {
        // For beta, we will just create placeholder blank images if GD is not available or 
        // to simplify the flow, we'll draw a basic image with text.
        
        if (!extension_loaded('gd')) {
            return;
        }

        $hero = null;
        foreach ($document->components as $c) {
            if ($c->type === 'Hero') {
                $hero = $c;
                break;
            }
        }
        
        $featuresCount = $hero ? ($hero->data['patterns_discovered'] ?? 43) : 43;

        $this->createImage($outputDir . '/square.png', 1080, 1080, $featuresCount);
        $this->createImage($outputDir . '/story.png', 1080, 1920, $featuresCount);
        $this->createImage($outputDir . '/landscape.png', 1200, 630, $featuresCount);
    }

    private function createImage(string $path, int $width, int $height, int $featuresCount): void
    {
        $image = imagecreatetruecolor($width, $height);
        
        // Colors
        $bg = imagecolorallocate($image, 10, 10, 10); // #0A0A0A
        $gold = imagecolorallocate($image, 212, 175, 55); // #D4AF37
        $white = imagecolorallocate($image, 253, 251, 247); // #FDFBF7
        
        imagefill($image, 0, 0, $bg);
        
        // Draw a basic border
        imagerectangle($image, 50, 50, $width - 50, $height - 50, $gold);
        
        // Basic text (GD built-in fonts are small, so we just use imagestring for now unless TTF is provided)
        // In a real scenario, imagettftext() should be used with Cormorant Garamond.
        
        imagestring($image, 5, $width / 2 - 100, $height / 2 - 60, "Personal Palm Blueprint", $white);
        imagestring($image, 5, $width / 2 - 60, $height / 2, "{$featuresCount} Patterns Found", $gold);
        imagestring($image, 5, $width / 2 - 80, $height / 2 + 60, "merarashifal.com", $white);
        
        imagepng($image, $path);
        imagedestroy($image);
    }
}
