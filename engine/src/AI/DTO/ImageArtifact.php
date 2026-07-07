<?php

namespace AIAnalysisEngine\AI\DTO;

class ImageArtifact implements InputArtifact
{
    public string $path;
    public string $mimeType;
    public ?int $width;
    public ?int $height;

    public function __construct(string $path, string $mimeType = 'image/jpeg', ?int $width = null, ?int $height = null)
    {
        $this->path = $path;
        $this->mimeType = $mimeType;
        $this->width = $width;
        $this->height = $height;
    }

    public function getType(): string
    {
        return 'image';
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }
}
