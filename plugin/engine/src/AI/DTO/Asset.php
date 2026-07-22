<?php

namespace AIAnalysisEngine\AI\DTO;

class Asset
{
    public const TYPE_PROMPT = 'prompt';
    public const TYPE_SCHEMA = 'schema';
    public const TYPE_CALIBRATION = 'calibration';
    public const TYPE_PREPROCESSING = 'preprocessing';

    public readonly string $id;
    public readonly string $type;
    public readonly string $version;
    public readonly array $data;

    public function __construct(string $id, string $type, string $version, array $data)
    {
        $this->id = $id;
        $this->type = $type;
        $this->version = $version;
        $this->data = $data;
    }
}
