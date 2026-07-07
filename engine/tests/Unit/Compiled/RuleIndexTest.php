<?php

namespace AIAnalysisEngine\Tests\Unit\Compiled;

use AIAnalysisEngine\Knowledge\Compiled\CompiledRule;
use AIAnalysisEngine\Knowledge\Compiled\RuleIndex;
use PHPUnit\Framework\TestCase;

class RuleIndexTest extends TestCase
{
    public function testIndexPopulatesAndRetrievesCorrectly()
    {
        $index = new RuleIndex();
        
        $rule1 = new CompiledRule();
        $rule1->uid = 'rule_1';
        $rule1->feature = 'life_line';
        $rule1->value = 'long';
        $rule1->analysis = 'palmistry';
        $rule1->section = 'career';
        $rule1->visibility = 'free';

        $rule2 = new CompiledRule();
        $rule2->uid = 'rule_2';
        $rule2->feature = 'life_line';
        $rule2->value = 'short';
        $rule2->analysis = 'palmistry';
        $rule2->section = 'health';
        $rule2->visibility = 'premium';
        
        $index->indexRule($rule1);
        $index->indexRule($rule2);

        $this->assertSame($rule1, $index->getByUid('rule_1'));
        $this->assertNull($index->getByUid('non_existent'));

        $lifeLineRules = $index->getByFeature('life_line');
        $this->assertCount(2, $lifeLineRules);

        $longLifeLine = $index->getByFeatureValue('life_line', 'long');
        $this->assertCount(1, $longLifeLine);
        $this->assertSame($rule1, $longLifeLine[0]);

        $premiumRules = $index->getByVisibility('premium');
        $this->assertCount(1, $premiumRules);
        $this->assertSame($rule2, $premiumRules[0]);

        $careerRules = $index->getBySection('career');
        $this->assertCount(1, $careerRules);
        $this->assertSame($rule1, $careerRules[0]);

        $afvRules = $index->getByAnalysisFeatureValue('palmistry', 'life_line', 'short');
        $this->assertCount(1, $afvRules);
        $this->assertSame($rule2, $afvRules[0]);
    }
}
