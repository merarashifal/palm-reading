<?php

namespace AIAnalysisEngine\AI\Preprocessing;

use AIAnalysisEngine\AI\DTO\ImageArtifact;

class ResizeOperation implements ImageOperationInterface
{
    private int $maxWidth;
    private int $maxHeight;

    public function __construct(int $maxWidth, int $maxHeight)
    {
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
    }

    public function apply(ImageArtifact $artifact): ImageArtifact
    {
        // Mock resize logic
        return new ImageArtifact($artifact->path, $artifact->mimeType, $this->maxWidth, $this->maxHeight);
    }
}
