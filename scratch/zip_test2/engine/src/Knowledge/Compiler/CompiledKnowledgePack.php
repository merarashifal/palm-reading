<?php

namespace AIAnalysisEngine\Knowledge\Compiler;

class CompiledKnowledgePack
{
    public array $manifest = [];
    public array $metadata = [];
    public array $statistics = [];
    public DictionaryCollection $dictionaries;
    public array $rules = [];
    public array $build = [];

    public function __construct()
    {
        $this->dictionaries = new DictionaryCollection();
    }
}
