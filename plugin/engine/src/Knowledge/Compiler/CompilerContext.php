<?php

namespace AIAnalysisEngine\Knowledge\Compiler;

use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Registry\KnowledgeRegistry;
use AIAnalysisEngine\Pipeline\PipelineContext;
use Psr\Log\LoggerInterface;

class CompilerContext extends PipelineContext
{
    public KnowledgeContext $knowledgeContext;
    public KnowledgeRegistry $registry;
    public CompiledKnowledgePack $pack;
    public ?LoggerInterface $logger;

    public function __construct(
        KnowledgeContext $knowledgeContext,
        KnowledgeRegistry $registry,
        CompiledKnowledgePack $pack,
        ?LoggerInterface $logger = null
    ) {
        $this->knowledgeContext = $knowledgeContext;
        $this->registry = $registry;
        $this->pack = $pack;
        $this->logger = $logger;
    }
}
