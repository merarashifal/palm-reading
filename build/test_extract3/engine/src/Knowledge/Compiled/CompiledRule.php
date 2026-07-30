<?php

namespace AIAnalysisEngine\Knowledge\Compiled;

class CompiledRule
{
    public string $uid = '';
    public string $language = '';
    public string $analysis = '';
    public string $feature = '';
    public string $value = '';
    public string $section = '';
    public string $visibility = 'free';
    public array $translations = [];
    public float $confidence = 1.0;
    public int $priority = 0;
    public array $relationships = [];
}
