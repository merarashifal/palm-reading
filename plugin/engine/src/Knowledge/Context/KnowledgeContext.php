<?php

namespace AIAnalysisEngine\Knowledge\Context;

class KnowledgeContext {
    private array $manifest;
    private array $configuration;
    private string $rootPath;
    private string $environment;
    private string $compilerVersion;
    private string $knowledgeVersion;
    private string $language;

    public function __construct(
        array $manifest,
        array $configuration,
        string $rootPath,
        string $environment,
        string $compilerVersion,
        string $knowledgeVersion,
        string $language
    ) {
        $this->manifest = $manifest;
        $this->configuration = $configuration;
        $this->rootPath = $rootPath;
        $this->environment = $environment;
        $this->compilerVersion = $compilerVersion;
        $this->knowledgeVersion = $knowledgeVersion;
        $this->language = $language;
    }

    public function getManifest(): array { return $this->manifest; }
    public function getConfiguration(): array { return $this->configuration; }
    public function getRootPath(): string { return $this->rootPath; }
    public function getEnvironment(): string { return $this->environment; }
    public function getCompilerVersion(): string { return $this->compilerVersion; }
    public function getKnowledgeVersion(): string { return $this->knowledgeVersion; }
    public function getLanguage(): string { return $this->language; }
}
