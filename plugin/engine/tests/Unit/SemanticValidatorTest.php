<?php

namespace AIAnalysisEngine\Tests\Unit;

use PHPUnit\Framework\TestCase;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Validator\SemanticValidator;

class SemanticValidatorTest extends TestCase {
    
    private KnowledgeContext $context;

    protected function setUp(): void {
        $fixturePath = dirname(__DIR__) . '/Fixtures/semantic_pack';
        $manifest = [
            'knowledge_pack' => 'Semantic Test Pack',
            'entry_points' => ['rules']
        ];
        
        $this->context = new KnowledgeContext(
            $manifest,
            [
                'paths' => [
                    'output' => dirname(__DIR__) . '/Fixtures/generated',
                    'dictionaries' => dirname(dirname(dirname(__DIR__))) . '/database/knowledge/dictionary'
                ]
            ],
            $fixturePath,
            'testing',
            '1.0.0',
            '1.0.0',
            'en'
        );
    }

    public function testSemanticValidator() {
        // Assume there is a rule that triggers a forbidden word or misses required words.
        $validator = new SemanticValidator();
        $result = $validator->validate($this->context);
        
        // This is a placeholder test until actual fixtures are placed in /Fixtures/semantic_pack
        // It validates that the validator runs without crashing.
        $this->assertEquals('SemanticValidator', $result->getValidator());
    }
}
