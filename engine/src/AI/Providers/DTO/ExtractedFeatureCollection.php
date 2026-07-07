<?php

namespace AIAnalysisEngine\AI\Providers\DTO;

class ExtractedFeatureCollection
{
    private array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getLines(): array
    {
        return $this->payload['lines'] ?? [];
    }

    public function getMounts(): array
    {
        return $this->payload['mounts'] ?? [];
    }

    public function getSigns(): array
    {
        return $this->payload['signs'] ?? [];
    }

    public function getHand(): ?string
    {
        return $this->payload['hand'] ?? null;
    }

    public function getImageQuality(): ?array
    {
        return $this->payload['image_quality'] ?? null;
    }
}
