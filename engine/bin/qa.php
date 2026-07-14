#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AIAnalysisEngine\Config\EngineConfig;
use AIAnalysisEngine\Facade\EngineFacade;
use AIAnalysisEngine\Config\EnvLoader;
use AIAnalysisEngine\Renderer\HtmlRenderer;

EnvLoader::load(__DIR__ . '/../.env');

$apiKey = getenv('GEMINI_API_KEY');
if (!$apiKey) {
    die("Error: GEMINI_API_KEY is not set in .env\n");
}

if ($argc < 2) {
    die("Usage: php bin/qa.php <image_path>\n");
}

$imagePath = $argv[1];
if (!file_exists($imagePath)) {
    die("Error: Image not found at $imagePath\n");
}

$config = new EngineConfig(
    $apiKey,
    getenv('GEMINI_MODEL') ?: 'gemini-2.5-flash',
    'Palm/Vision/2.5-flash/v1',
    __DIR__ . '/../knowledge/palmistry_v1.json',
    __DIR__ . '/../storage'
);

echo "Starting QA Run on: $imagePath\n";

try {
    $result = EngineFacade::analyze($imagePath, $config);
    
    // Get the run ID from metadata
    $runId = $result->metadata['run_id'] ?? 'unknown_run';
    $runStorage = __DIR__ . "/../storage/runs/$runId";
    
    // The WordPress plugin would normally generate the HTML from the ReportModel
    // For our QA script, we'll use HtmlRenderer to do it directly
    $renderer = new HtmlRenderer();
    $html = $renderer->render($result->reportModel);
    
    $htmlPath = "$runStorage/report.html";
    file_put_contents($htmlPath, $html);
    
    echo "====================================\n";
    echo "QA Run Complete: $runId\n";
    echo "Features Detected: " . $result->metadata['features_detected'] . "\n";
    echo "Insights Generated: " . $result->metadata['insights_generated'] . "\n";
    echo "Time: " . $result->metadata['total_time_ms'] . "ms\n";
    echo "Run Artifacts: $runStorage\n";
    echo "Report HTML: $htmlPath\n";
    
} catch (Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
    exit(1);
}
