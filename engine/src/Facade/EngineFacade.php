<?php

namespace AIAnalysisEngine\Facade;

use AIAnalysisEngine\AI\Providers\DTO\NormalizedFeatureCollection;
use AIAnalysisEngine\AI\Providers\DTO\NormalizedFeature;
use AIAnalysisEngine\Inference\Runtime\InferenceRuntime;
use AIAnalysisEngine\Presentation\PresentationEngine;
use AIAnalysisEngine\Renderer\HtmlRenderer;
use AIAnalysisEngine\Facade\DTO\EngineResult;

class EngineFacade
{
    /**
     * Executes the full pipeline from Image to HTML Report.
     */
    public static function analyze(string $imagePath): EngineResult
    {
        $startTime = microtime(true);
        $runId = 'run-' . date('Ymd-His') . '-' . substr(md5(uniqid()), 0, 6);
        
        // Ensure storage directories exist
        $baseDir = realpath(__DIR__ . '/../../..');
        $storageDir = $baseDir . "/engine/storage/reports/$runId";
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0777, true);
        }

        // 1. Image Upload & Gemini Extraction (MOCKED FOR SPRINT A5 - REAL INTEGRATION IN SPRINT A6)
        // We inject the 10 features directly to ensure a fast, robust local demo for WordPress.
        $features = new NormalizedFeatureCollection();
        $features->addFeature(new NormalizedFeature('life_line', 0.95, ['length' => 'long', 'depth' => 'deep'], []));
        $features->addFeature(new NormalizedFeature('heart_line', 0.90, ['depth' => 'deep'], ['broken_segments']));
        $features->addFeature(new NormalizedFeature('head_line', 0.88, [], ['fork_endpoint']));
        $features->addFeature(new NormalizedFeature('fate_line', 0.85, ['depth' => 'deep'], []));
        $features->addFeature(new NormalizedFeature('star', 0.99, ['location' => 'mount_jupiter'], []));
        $features->addFeature(new NormalizedFeature('mount_venus', 0.92, ['prominence' => 'prominent'], []));

        // 2. Inference
        $runtime = new InferenceRuntime();
        $knowledgePackPath = $baseDir . '/engine/knowledge/palmistry_v1.json';
        $inferenceResult = $runtime->run($knowledgePackPath, $features);

        // 3. Presentation Layer
        $presentationEngine = new PresentationEngine();
        $reportModel = $presentationEngine->buildReport($inferenceResult);

        // 4. Rendering Layer
        $renderer = new HtmlRenderer();
        $html = $renderer->render($reportModel);

        $totalTime = microtime(true) - $startTime;

        $metadata = [
            'run_id' => $runId,
            'image_path' => $imagePath,
            'total_ms' => round($totalTime * 1000),
            'features_detected' => count($features->getAllFeatures()),
            'insights_generated' => count($inferenceResult->insights)
        ];
        
        file_put_contents("$storageDir/report.html", $html);
        file_put_contents("$storageDir/metadata.json", json_encode($metadata));

        return new EngineResult($reportModel, $html, $metadata);
    }
}
