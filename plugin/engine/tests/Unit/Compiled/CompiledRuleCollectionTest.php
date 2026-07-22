<?php

namespace AIAnalysisEngine\Tests\Unit\Compiled;

use AIAnalysisEngine\Knowledge\Compiled\CompiledRule;
use AIAnalysisEngine\Knowledge\Compiled\CompiledRuleCollection;
use PHPUnit\Framework\TestCase;

class CompiledRuleCollectionTest extends TestCase
{
    public function testCollectionAddsAndDelegatesToIndex()
    {
        $collection = new CompiledRuleCollection();
        
        $rule = new CompiledRule();
        $rule->uid = 'test_uid';
        $rule->feature = 'heart_line';
        $rule->visibility = 'premium';
        
        $collection->add($rule);
        
        $this->assertCount(1, $collection);
        $this->assertCount(1, $collection->all());
        
        $this->assertSame($rule, $collection->get('test_uid'));
        $this->assertSame($rule, $collection->findByUid('test_uid'));
        
        $featureRules = $collection->findByFeature('heart_line');
        $this->assertCount(1, $featureRules);
        
        $visRules = $collection->findByVisibility('premium');
        $this->assertCount(1, $visRules);
    }
    
    public function testIterableSupport()
    {
        $collection = new CompiledRuleCollection();
        
        $rule1 = new CompiledRule();
        $rule2 = new CompiledRule();
        
        $collection->add($rule1);
        $collection->add($rule2);
        
        $count = 0;
        foreach ($collection as $r) {
            $count++;
            $this->assertInstanceOf(CompiledRule::class, $r);
        }
        
        $this->assertEquals(2, $count);
    }
}
