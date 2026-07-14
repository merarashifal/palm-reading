<?php

namespace AIAnalysisEngine\Facade;

use AIAnalysisEngine\Config\EngineConfig;
use AIAnalysisEngine\Exception\EngineException;
use AIAnalysisEngine\Validator\ImageValidator;
use AIAnalysisEngine\AI\Providers\Gemini\GeminiClient;
use AIAnalysisEngine\AI\Providers\Gemini\GeminiProviderPipeline;
use AIAnalysisEngine\Inference\Runtime\InferenceRuntime;
use AIAnalysisEngine\Presentation\PresentationEngine;
use AIAnalysisEngine\Facade\DTO\EngineResult;
use Throwable;

class EngineFacade
{
    /**
     * Executes the full pipeline from Image to ReportModel.
     * Does NOT generate HTML. That is left to the client (e.g., WordPress plugin).
     */
    public static function analyze(string $imagePath, EngineConfig $config): EngineResult
    {
        $startTime = microtime(true);
        $runId = 'run_' . date('Ymd_His') . '_' . substr(md5(uniqid()), 0, 6);
        
        $storageDir = $config->storagePath . "/runs/$runId";
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0777, true);
        }

        try {
            // 1. Validate Image
            $validator = new ImageValidator();
            $validator->validate($imagePath);

            // Copy original image for record
            copy($imagePath, $storageDir . '/image.' . pathinfo($imagePath, PATHINFO_EXTENSION));

            // 2. Extract Features via Gemini
            $client = new GeminiClient(
                $config->geminiApiKey,
                $config->geminiModel,
                'https://generativelanguage.googleapis.com/v1beta/models/' . $config->geminiModel . ':generateContent',
                $storageDir
            );
            $pipeline = new GeminiProviderPipeline($client);
            $promptPath = realpath(__DIR__ . '/../../knowledge/' . $config->promptVersion . '.txt');
            if (!$promptPath) {
                // fallback path if realpath fails
                $promptPath = dirname(__DIR__, 3) . '/knowledge/' . $config->promptVersion . '.txt';
            }
            
            $features = $pipeline->run($imagePath, $promptPath);
            file_put_contents($storageDir . '/normalized.json', json_encode($features->all(), JSON_PRETTY_PRINT));

            // 3. Inference Runtime
            $runtime = new InferenceRuntime();
            $inferenceResult = $runtime->run($config->knowledgePackPath, $features);
            file_put_contents($storageDir . '/inference.json', json_encode($inferenceResult, JSON_PRETTY_PRINT));

            // 4. Presentation Layer
            $composer = new \AIAnalysisEngine\Presentation\ReportComposer();
            
            // Re-fetch quality from Gemini if available
            $qualityScore = 'Excellent'; // default
            
            $reportModel = $composer->compose($inferenceResult, ['image_quality' => $qualityScore]);
            file_put_contents($storageDir . '/report.json', json_encode($reportModel, JSON_PRETTY_PRINT));

            $totalTimeMs = round((microtime(true) - $startTime) * 1000);

            // Fetch Gemini Metrics if they exist
            $geminiMetricsPath = $storageDir . '/gemini_metrics.json';
            $geminiMetrics = file_exists($geminiMetricsPath) ? json_decode(file_get_contents($geminiMetricsPath), true) : [];

            $metadata = [
                'run_id' => $runId,
                'engine_version' => '0.9.0', // RC1
                'knowledge_pack' => basename($config->knowledgePackPath),
                'prompt_version' => $config->promptVersion,
                'gemini_model' => $config->geminiModel,
                'total_time_ms' => $totalTimeMs,
                'features_detected' => count($features->all()),
                'insights_generated' => count($inferenceResult->insights),
                'gemini_execution' => $geminiMetrics
            ];
            
            file_put_contents($storageDir . '/metrics.json', json_encode($metadata, JSON_PRETTY_PRINT));
            
            // Log Success Status
            $status = [
                'status' => 'success',
                'stage' => 'complete'
            ];
            file_put_contents($storageDir . '/status.json', json_encode($status, JSON_PRETTY_PRINT));

            // Return EngineResult without HTML
            return new EngineResult($reportModel, '', $metadata);

        } catch (EngineException $e) {
            self::logFailure($storageDir, $e->getErrorCode(), $e->getMessage(), $e);
            throw $e;
        } catch (Throwable $e) {
            self::logFailure($storageDir, 'SYS_001', 'Unexpected exception occurred.', $e);
            throw new EngineException('SYS_001', 'Unexpected system error: ' . $e->getMessage());
        }
    }

    private static function logFailure(string $storageDir, string $errorCode, string $message, Throwable $e): void
    {
        $status = [
            'status' => 'failed',
            'error_code' => $errorCode,
            'message' => $message,
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ];
        file_put_contents($storageDir . '/status.json', json_encode($status, JSON_PRETTY_PRINT));
    }
}
