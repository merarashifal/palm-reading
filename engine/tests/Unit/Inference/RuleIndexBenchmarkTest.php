<?php

namespace AIAnalysisEngine\Tests\Unit\Inference;

use PHPUnit\Framework\TestCase;
use AIAnalysisEngine\Tests\Fixtures\FixtureFactory;
use AIAnalysisEngine\Inference\DTO\Evidence;
use AIAnalysisEngine\Knowledge\Compiled\RuleIndex;

class RuleIndexBenchmarkTest extends TestCase
{
    /**
     * @group benchmark
     */
    public function testLookupPerformance()
    {
        $sizes = [50, 500, 5000, 50000];
        $evidenceCounts = [1, 100, 1000];

        echo "\nRuleIndex O(1) Lookup Benchmarks:\n";
        echo str_pad("Rules", 10) . str_pad("Evidence", 12) . str_pad("Duration (ms)", 15) . str_pad("Matches", 10) . "\n";
        echo str_repeat("-", 47) . "\n";

        foreach ($sizes as $size) {
            $knowledge = FixtureFactory::create($size);
            /** @var RuleIndex $index */
            $index = $knowledge->rules->index;

            foreach ($evidenceCounts as $evCount) {
                // Generate dummy evidence
                $evidences = [];
                $analyses = ['life_line', 'heart_line', 'fate_line', 'head_line', 'mounts', 'signs'];
                $values = ['long', 'short', 'broken', 'chained', 'deep', 'faint', 'starred'];
                
                for ($i = 0; $i < $evCount; $i++) {
                    $evidences[] = new Evidence(
                        'ev_' . $i,
                        $analyses[array_rand($analyses)],
                        $values[array_rand($values)],
                        0.9
                    );
                }

                $startTime = microtime(true);
                $totalMatches = 0;

                foreach ($evidences as $ev) {
                    $matches = $index->match($ev);
                    $totalMatches += count($matches);
                }

                $durationMs = (microtime(true) - $startTime) * 1000;

                echo str_pad($size, 10) . str_pad($evCount, 12) . str_pad(round($durationMs, 2), 15) . str_pad($totalMatches, 10) . "\n";
                
                // Extremely loose assertion just to ensure it doesn't timeout
                $this->assertLessThan(2000, $durationMs, "Lookup took too long for size {$size} and evidence {$evCount}");
            }
        }
    }
}
