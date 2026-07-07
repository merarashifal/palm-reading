<?php

namespace AIAnalysisEngine\Knowledge\Compiler\Pipeline;

use AIAnalysisEngine\Knowledge\Compiler\ManifestCompiler;
use AIAnalysisEngine\Knowledge\Compiler\DictionaryCompiler;
use AIAnalysisEngine\Knowledge\Compiler\Rule\RuleCompiler;
use AIAnalysisEngine\Knowledge\Compiler\MetadataCompiler;
use AIAnalysisEngine\Knowledge\Compiler\StatisticsCompiler;
use Psr\Log\LoggerInterface;

class CompilerFactory
{
    /**
     * Instantiates the CompilerPipeline and pre-registers the standard compiler stages in strict order.
     */
    public static function createDefaultPipeline(?LoggerInterface $logger = null): CompilerPipeline
    {
        $pipeline = new CompilerPipeline();
        $pipeline->setLogger($logger);

        // Registration Order:
        // 10: Manifest - The core contract that dictates what is loaded.
        // 20: Dictionary - Compiles the dictionaries referenced by the manifest.
        // 30: Rule - Compiles the rule sets referenced by the manifest.
        // 40: Metadata - Injects version and environment metadata.
        // 50: Statistics - Aggregates counts purely from memory.
        
        $pipeline->register(new ManifestCompiler(), 10);
        $pipeline->register(new DictionaryCompiler(), 20);
        $pipeline->register(new RuleCompiler(), 30);
        $pipeline->register(new MetadataCompiler(), 40);
        $pipeline->register(new StatisticsCompiler(), 50);

        return $pipeline;
    }
}
