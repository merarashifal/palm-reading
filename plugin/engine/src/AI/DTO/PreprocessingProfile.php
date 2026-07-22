<?php

namespace AIAnalysisEngine\AI\DTO;

class PreprocessingProfile
{
    public readonly string $id;
    public readonly string $name;
    /** @var string[] List of operation class names or DI keys */
    public readonly array $operations;

    public function __construct(string $id, string $name, array $operations)
    {
        $this->id = $id;
        $this->name = $name;
        $this->operations = $operations;
    }
}
