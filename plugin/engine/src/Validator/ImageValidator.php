<?php

namespace AIAnalysisEngine\Validator;

use AIAnalysisEngine\Exception\EngineException;

class ImageValidator
{
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const MIN_WIDTH = 500;
    private const MIN_HEIGHT = 500;

    /**
     * Validates an image file before it's sent to Gemini.
     * Throws EngineException with specific error codes if validation fails.
     */
    public function validate(string $imagePath): void
    {
        if (!file_exists($imagePath)) {
            throw new EngineException('SYS_001', 'Image file does not exist at path.');
        }

        // 1. Check Format
        $mimeType = mime_content_type($imagePath);
        if (!$mimeType || !in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            throw new EngineException('IMG_001', 'Unsupported format. Please upload JPG, PNG, or WEBP.');
        }

        // 2. Check File Size (e.g., minimum 10KB to avoid empty files)
        $filesize = filesize($imagePath);
        if ($filesize < 10240) {
            throw new EngineException('IMG_002', 'Image file is too small or empty.');
        }

        // 3. Can GD load it? And Check Resolution
        $imageSize = @getimagesize($imagePath);
        if ($imageSize === false) {
            throw new EngineException('IMG_003', 'Image is corrupted or cannot be read.');
        }

        $width = $imageSize[0];
        $height = $imageSize[1];

        if ($width < self::MIN_WIDTH || $height < self::MIN_HEIGHT) {
            throw new EngineException('IMG_004', 'Resolution too low. Minimum ' . self::MIN_WIDTH . 'x' . self::MIN_HEIGHT . ' required.');
        }
    }
}
