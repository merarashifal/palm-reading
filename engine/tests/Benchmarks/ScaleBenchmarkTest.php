<?php

namespace AIAnalysisEngine\Tests\Benchmarks;

use PHPUnit\Framework\TestCase;
use AIAnalysisEngine\Tests\Fixtures\FixtureFactory;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\PermissionContext;
use AIAnalysisEngine\Inference\DTO\EngineConfiguration;
use AIAnalysisEngine\Inference\Pipeline\InferenceFactory;

class ScaleBenchmarkTest extends TestCase
{
    /**
     * @group benchmark
     */
    public function testMassiveScale()
    {
        $sizes = [100, 500, 5000, 10000, 25000, 50000, 100000];
        
        $outputDir = __DIR__ . '/../../../release';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $report = [];
        $htmlRows = [];

        foreach ($sizes as $size) {
            $knowledge = FixtureFactory::create($size);
            $permission = new PermissionContext();
            $config = new EngineConfiguration();

            $rawPayload = [];
            for ($i=0; $i<100; $i++) {
                $rawPayload[] = [
                    'analysis' => 'life_line',
                    'feature' => 'life_line',
                    'value' => 'broken',
                    'confidence' => 0.9
                ];
            }

            $context = new InferenceContext($rawPayload, $knowledge, $permission, $config);
            $pipeline = InferenceFactory::createPipeline();
            
            $startTime = microtime(true);
            $startMemory = memory_get_usage();
            
            $pipeline->execute($context);

            $duration = microtime(true) - $startTime;
            $peakMemory = memory_get_peak_usage() / 1024 / 1024; // MB
            
            // Extract lookup time from trace
            $lookupTime = 0;
            foreach ($context->trace->getStages() as $stage) {
                if ($stage['stage'] === 'RuleLookup') {
                    $lookupTime = $stage['duration_ms'];
                    break;
                }
            }

            $report[] = [
                'rules' => $size,
                'duration_sec' => round($duration, 3),
                'peak_memory_mb' => round($peakMemory, 2),
                'lookup_ms' => round($lookupTime, 2)
            ];

            $htmlRows[] = "<tr><td>100</td><td>{$size}</td><td>" . round($duration, 3) . "s</td><td>" . round($peakMemory, 2) . " MB</td><td>" . round($lookupTime, 2) . " ms</td></tr>";

            // Performance gates for 100k
            if ($size === 100000) {
                $this->assertLessThan(3.0, $duration, "Pipeline duration exceeded 3s for 100k rules.");
                $this->assertLessThan(128.0, $peakMemory, "Peak memory exceeded 128MB for 100k rules.");
                $this->assertLessThan(1.0, $lookupTime, "O(1) RuleLookup exceeded 1ms for 100k rules.");
            }
        }

        // Dump JSON
        file_put_contents($outputDir . '/benchmark.json', json_encode(['benchmarks' => $report], JSON_PRETTY_PRINT));

        // Dump HTML
        $html = "<html><head><title>Benchmarks</title></head><body><h1>Engine Benchmarks</h1><table border='1'><tr><th>Evidence</th><th>Rules</th><th>Duration</th><th>Peak Memory</th><th>Lookup Time</th></tr>";
        $html .= implode("\n", $htmlRows);
        $html .= "</table></body></html>";
        file_put_contents($outputDir . '/benchmark.html', $html);
        
        $this->assertFileExists($outputDir . '/benchmark.json');
        $this->assertFileExists($outputDir . '/benchmark.html');
    }
}
