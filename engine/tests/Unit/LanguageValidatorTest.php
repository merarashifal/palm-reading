<?php

namespace AIAnalysisEngine\Tests\Unit;

use PHPUnit\Framework\TestCase;
use AIAnalysisEngine\Knowledge\Validator\LanguageValidator;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Validator\ValidationResult;

class LanguageValidatorTest extends TestCase {
    
    private KnowledgeContext $context;

    protected function setUp(): void {
        // Setup a mock context pointing to our fixtures
        $fixturePath = dirname(__DIR__) . '/Fixtures/language_pack';
        $manifest = [
            'knowledge_pack' => 'Test Pack',
            'entry_points' => ['rules']
        ];
        
        $this->context = new KnowledgeContext(
            $manifest,
            ['paths' => ['output' => dirname(__DIR__) . '/Fixtures/generated']],
            $fixturePath,
            'testing',
            '1.0.0',
            '1.0.0',
            'en'
        );
    }

    public function testValidateLanguageRules() {
        $validator = new LanguageValidator();
        $result = $validator->validate($this->context);
        
        $errors = $result->getErrors();
        $warnings = $result->getWarnings();
        
        // Assert we caught the missing english error
        $errorStr = implode(' ', $errors);
        $warningStr = implode(' ', $warnings);
        
        $this->assertStringContainsString('Missing English translation', $errorStr);
        $this->assertStringContainsString('Empty Hindi translation', $errorStr);
        $this->assertStringContainsString('Placeholder', $errorStr);
        $this->assertStringContainsString('Illegal HTML', $errorStr);
        
        $this->assertStringContainsString('Length ratio warning', $warningStr);
        $this->assertStringContainsString('Duplicate paragraph', $warningStr);
    }
}
