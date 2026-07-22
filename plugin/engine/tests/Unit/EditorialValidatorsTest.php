<?php

namespace AIAnalysisEngine\Tests\Unit;

use PHPUnit\Framework\TestCase;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Validator\Editorial\StructureValidator;
use AIAnalysisEngine\Knowledge\Validator\Editorial\ToneValidator;
use AIAnalysisEngine\Knowledge\Validator\Editorial\PhraseValidator;

class EditorialValidatorsTest extends TestCase {
    
    private KnowledgeContext $context;

    protected function setUp(): void {
        $fixturePath = dirname(__DIR__) . '/Fixtures/editorial_pack';
        $manifest = [
            'knowledge_pack' => 'Editorial Test Pack',
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

    public function testStructureValidator() {
        $validator = new StructureValidator();
        $result = $validator->validate($this->context);
        
        $errors = $result->getErrors();
        $errorStr = implode(' ', $errors);
        
        $this->assertStringContainsString('Missing curiosity section', $errorStr);
        $this->assertStringContainsString('Missing observation or interpretation', $errorStr);
    }
    
    public function testToneValidator() {
        $validator = new ToneValidator();
        $result = $validator->validate($this->context);
        
        $errors = $result->getErrors();
        $errorStr = implode(' ', $errors);
        
        $this->assertStringContainsString('Tone mismatch', $errorStr);
    }
    
    public function testPhraseValidator() {
        $validator = new PhraseValidator();
        $result = $validator->validate($this->context);
        
        $warnings = $result->getWarnings();
        $warningStr = implode(' ', $warnings);
        
        $this->assertStringContainsString('is used in more than 50% of the rules', $warningStr);
    }
}
