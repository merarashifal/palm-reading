<?php

namespace AIAnalysisEngine\Knowledge\Generator;

use AIAnalysisEngine\Knowledge\Compiled\CompiledKnowledgePack;
use AIAnalysisEngine\Pipeline\PipelineContext;
use Psr\Log\LoggerInterface;

class BuildContext extends PipelineContext
{
    public CompiledKnowledgePack $pack;
    public string $baseOutputDir;
    public string $latestDir;
    public string $historyDir;
    public ?LoggerInterface $logger;

    public function __construct(
        CompiledKnowledgePack $pack,
        string $baseOutputDir,
        ?LoggerInterface $logger = null
    ) {
        $this->pack = $pack;
        $this->baseOutputDir = rtrim($baseOutputDir, '/\\');
        $this->logger = $logger;

        // Ensure directories exist
        $this->latestDir = $this->baseOutputDir . '/latest';
        
        $version = $pack->metadata['build']['knowledge_pack_version'] ?? 'unknown';
        $this->historyDir = $this->baseOutputDir . '/history/' . $version;
        
        $this->ensureDirectories();
    }

    private function ensureDirectories(): void
    {
        if (!is_dir($this->latestDir)) {
            mkdir($this->latestDir, 0755, true);
        }
        if (!is_dir($this->historyDir)) {
            mkdir($this->historyDir, 0755, true);
        }
    }
}
