<?php

namespace AIAnalysisEngine\Tests\Unit\Compiler\Rule;

use AIAnalysisEngine\Knowledge\Compiled\CompiledRule;
use AIAnalysisEngine\Knowledge\Compiled\CompiledRuleCollection;
use AIAnalysisEngine\Knowledge\Compiler\Rule\RuleOptimizer;
use PHPUnit\Framework\TestCase;

class RuleOptimizerTest extends TestCase
{
    public function testSortsAndDeduplicates()
    {
        $collection = new CompiledRuleCollection();
        
        $rule1 = new CompiledRule();
        $rule1->uid = 'r_1';
        $rule1->priority = 5;
        
        $rule2 = new CompiledRule();
        $rule2->uid = 'r_2';
        $rule2->priority = 10;
        
        $rule3 = new CompiledRule(); // Duplicate UID of r_1, but higher priority
        $rule3->uid = 'r_1';
        $rule3->priority = 15;
        
        $collection->add($rule1);
        $collection->add($rule2);
        $collection->add($rule3);
        
        $optimizer = new RuleOptimizer();
        $optimizedCollection = $optimizer->optimize($collection);
        
        $rules = $optimizedCollection->all();
        
        $this->assertCount(2, $rules);
        
        // Highest priority first
        $this->assertEquals('r_1', $rules[0]->uid);
        $this->assertEquals(15, $rules[0]->priority); // Kept the highest priority duplicate
        
        $this->assertEquals('r_2', $rules[1]->uid);
        $this->assertEquals(10, $rules[1]->priority);
    }
}
