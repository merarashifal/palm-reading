<?php

namespace AIAnalysisEngine\Tests\Performance\Compiled;

use AIAnalysisEngine\Knowledge\Compiled\CompiledRule;
use AIAnalysisEngine\Knowledge\Compiled\CompiledRuleCollection;
use PHPUnit\Framework\TestCase;

class CompiledRuleCollectionBenchmarkTest extends TestCase
{
    public function testBenchmarkInsertAndLookup10000Rules()
    {
        $collection = new CompiledRuleCollection();
        $numRules = 10000;
        
        $startMem = memory_get_usage();
        $startTime = microtime(true);
        
        for ($i = 0; $i < $numRules; $i++) {
            $rule = new CompiledRule();
            $rule->uid = 'r_' . $i;
            $rule->feature = 'f_' . ($i % 50); // 50 distinct features
            $rule->value = 'v_' . ($i % 10);
            $rule->visibility = ($i % 5 === 0) ? 'premium' : 'free';
            
            $collection->add($rule);
        }
        
        $insertTime = microtime(true) - $startTime;
        
        $startTime = microtime(true);
        
        // 1000 lookups
        for ($i = 0; $i < 1000; $i++) {
            $collection->findByFeature('f_25');
            $collection->get('r_5000');
        }
        
        $lookupTime = microtime(true) - $startTime;
        $memUsed = memory_get_usage() - $startMem;
        
        echo "\n[BENCHMARK] 10,000 Rules Insert Time: " . round($insertTime * 1000, 2) . " ms\n";
        echo "[BENCHMARK] 1,000 Lookups Time: " . round($lookupTime * 1000, 2) . " ms\n";
        echo "[BENCHMARK] Memory Used: " . round($memUsed / 1024 / 1024, 2) . " MB\n";
        
        $this->assertTrue(true); // Prevent "no assertions" warning
    }
}
