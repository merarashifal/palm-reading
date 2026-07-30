<?php

namespace AIAnalysisEngine\AI\DTO;

class BoundingRegion
{
    public const TYPE_POLYGON = 'polygon';
    public const TYPE_RECTANGLE = 'rectangle';
    public const TYPE_LANDMARK = 'landmark';

    public string $type;
    /** @var array<array{x: float, y: float}> */
    public array $points;

    public function __construct(string $type, array $points)
    {
        $this->type = $type;
        $this->points = $points;
    }
}
