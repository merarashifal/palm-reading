<?php

namespace AIAnalysisEngine\Tests\Unit\Compiler\Rule;

use AIAnalysisEngine\Knowledge\Compiler\Rule\RuleCollectionBuilder;
use AIAnalysisEngine\Knowledge\Compiler\Rule\RuleNormalizer;
use PHPUnit\Framework\TestCase;

class RuleCollectionBuilderTest extends TestCase
{
    public function testBuildsCollectionFromRawArrays()
    {
        $rawRules = [
            ['rule_uid' => 'r_1', 'feature' => 'f1'],
            ['rule_uid' => 'r_2', 'feature' => 'f2']
        ];
        
        $normalizer = new RuleNormalizer();
        $builder = new RuleCollectionBuilder();
        
        $collection = $builder->build($rawRules, $normalizer);
        
        $this->assertCount(2, $collection);
        $this->assertEquals('r_1', $collection->get('r_1')->uid);
        $this->assertEquals('r_2', $collection->get('r_2')->uid);
    }
}
