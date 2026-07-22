<?php

namespace AIAnalysisEngine\Tests\Unit\Compiler\Rule;

use AIAnalysisEngine\Knowledge\Compiled\CompiledRule;
use AIAnalysisEngine\Knowledge\Compiler\Rule\RuleNormalizer;
use PHPUnit\Framework\TestCase;

class RuleNormalizerTest extends TestCase
{
    public function testNormalizesRawArrayToCompiledRule()
    {
        $raw = [
            'rule_uid' => 'r_123',
            'language' => 'hi',
            'analysis' => 'palmistry',
            'feature' => 'heart_line',
            'value' => 'chained',
            'section' => 'love',
            'visibility' => 'premium',
            'translations' => ['en' => 'Chained heart line'],
            'confidence' => 0.85,
            'priority' => 10,
            'relationships' => ['r_124']
        ];
        
        $normalizer = new RuleNormalizer();
        $rule = $normalizer->normalize($raw);
        
        $this->assertInstanceOf(CompiledRule::class, $rule);
        $this->assertEquals('r_123', $rule->uid);
        $this->assertEquals('hi', $rule->language);
        $this->assertEquals('palmistry', $rule->analysis);
        $this->assertEquals('heart_line', $rule->feature);
        $this->assertEquals('chained', $rule->value);
        $this->assertEquals('love', $rule->section);
        $this->assertEquals('premium', $rule->visibility);
        $this->assertEquals(['en' => 'Chained heart line'], $rule->translations);
        $this->assertEquals(0.85, $rule->confidence);
        $this->assertEquals(10, $rule->priority);
        $this->assertEquals(['r_124'], $rule->relationships);
    }

    public function testNormalizesWithMissingOptionalFields()
    {
        $raw = [
            'rule_uid' => 'r_456',
            'analysis' => 'numerology',
            'feature' => 'life_path',
            'value' => '7',
            'section' => 'career'
        ];
        
        $normalizer = new RuleNormalizer();
        $rule = $normalizer->normalize($raw);
        
        $this->assertInstanceOf(CompiledRule::class, $rule);
        $this->assertEquals('r_456', $rule->uid);
        $this->assertEquals('en', $rule->language); // Default
        $this->assertEquals('numerology', $rule->analysis);
        $this->assertEquals('free', $rule->visibility); // Default
        $this->assertEquals(1.0, $rule->confidence); // Default
        $this->assertEquals(0, $rule->priority); // Default
        $this->assertEquals([], $rule->translations); // Default
        $this->assertEquals([], $rule->relationships); // Default
    }
}
