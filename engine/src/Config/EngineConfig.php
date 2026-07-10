<?php

namespace AIAnalysisEngine\Config;

class EngineConfig
{
    public string $geminiApiKey;
    public string $geminiModel;
    public string $promptVersion;
    public string $knowledgePackPath;
    public string $storagePath;

    public function __construct(
        string $geminiApiKey,
        string $geminiModel,
        string $promptVersion,
        string $knowledgePackPath,
        string $storagePath
    ) {
        $this->geminiApiKey = $geminiApiKey;
        $this->geminiModel = $geminiModel;
        $this->promptVersion = $promptVersion;
        $this->knowledgePackPath = $knowledgePackPath;
        $this->storagePath = $storagePath;
    }
}
