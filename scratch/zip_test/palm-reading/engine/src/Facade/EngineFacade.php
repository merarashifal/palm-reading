<?php

namespace AIAnalysisEngine\Facade;

use AIAnalysisEngine\Config\EngineConfig;
use AIAnalysisEngine\Exception\EngineException;
use AIAnalysisEngine\Validator\ImageValidator;
use AIAnalysisEngine\AI\Providers\Gemini\GeminiClient;
use AIAnalysisEngine\AI\Providers\Gemini\GeminiProviderPipeline;
use AIAnalysisEngine\Inference\Runtime\InferenceRuntime;
use AIAnalysisEngine\Facade\DTO\EngineResult;
use AIAnalysisEngine\Storage\AnalysisRepository;
use AIAnalysisEngine\Renderer\HtmlRenderer;
use AIAnalysisEngine\Renderer\PdfRenderer;
use Throwable;

class EngineFacade
{
    public static function analyze(string $imagePath, EngineConfig $config): EngineResult
    {
        $startTime = microtime(true);
        $repo = new AnalysisRepository($config->storagePath);
        $reportId = $repo->generateReportId();
        $runId = $repo->generateRunId();
        $storageDir = $repo->getStorageDir($reportId);

        try {
            // 1. Validate Image
            $validator = new ImageValidator();
            $validator->validate($imagePath);

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
                $promptPath = dirname(__DIR__, 3) . '/knowledge/' . $config->promptVersion . '.txt';
            }
            
            $features = $pipeline->run($imagePath, $promptPath);

            // 3. Inference Runtime
            $runtime = new InferenceRuntime();
            $inferenceResult = $runtime->run($config->knowledgePackPath, $features, $config->language);

            // 4. Presentation Layer
            $composer = new \AIAnalysisEngine\Presentation\ReportComposer();
            $qualityScore = 'Excellent'; // default
            
            $composeMetadata = [
                'image_quality' => $qualityScore,
                'gemini_model' => $config->geminiModel,
                'user_name' => 'Tushar',
                'raw_features' => $features->all()
            ];

            // Generate Free Document
            $freeDocument = $composer->compose($inferenceResult, $composeMetadata, false);
            
            // Generate Premium Document
            $premiumDocument = $composer->compose($inferenceResult, $composeMetadata, true);

            // 5. Renderers
            $htmlRenderer = new HtmlRenderer();
            $freeHtml = $htmlRenderer->render($freeDocument);
            $premiumHtml = $htmlRenderer->render($premiumDocument);

            $pdfPath = $storageDir . '/premium.pdf';
            if (class_exists('Mpdf\Mpdf')) {
                $pdfRenderer = new PdfRenderer();
                $pdfRenderer->renderToFile($premiumDocument, $pdfPath);
            }
            
            $socialRenderer = new \AIAnalysisEngine\Renderer\SocialCardRenderer();
            $socialRenderer->renderToFiles($premiumDocument, $storageDir);

            // Metrics and Metadata
            $totalTimeMs = round((microtime(true) - $startTime) * 1000);
            $geminiMetricsPath = $storageDir . '/gemini_metrics.json';
            $geminiMetrics = file_exists($geminiMetricsPath) ? json_decode(file_get_contents($geminiMetricsPath), true) : [];

            $lockedInsights = 0;
            foreach ($inferenceResult->insights as $insight) {
                if ($insight->visibility === 'premium') $lockedInsights++;
            }

            $metadata = [
                'report_id' => $reportId,
                'run_id' => $runId,
                'session_id' => 'ses_' . uniqid(),
                'visitor_id' => 'vis_' . uniqid(),
                'language' => $config->language,
                'created_at' => date('c'),
                'engine_version' => '1.0.0-beta',
                'knowledge_version' => basename($config->knowledgePackPath),
                'renderer_version' => '1.0.0',
                'report_template' => 'v1',
                'analysis_status' => 'completed',
                'features_detected' => count($features->all()),
                'insights_generated' => count($inferenceResult->insights),
                'locked_insights' => $lockedInsights,
                'confidence' => 95,
                'image_quality' => $qualityScore,
                'latency' => $totalTimeMs,
                'total_time_ms' => $totalTimeMs,
                'gemini_execution' => $geminiMetrics
            ];
            
            // 6. Save Lifecycle
            $repo->saveLifecycle(
                $reportId,
                $runId,
                $imagePath,
                $features->all(),
                $inferenceResult->toArray(),
                $freeDocument->toArray(),
                $metadata,
                $freeHtml,
                $premiumHtml,
                file_exists($pdfPath) ? $pdfPath : null
            );

            // Log Success Status
            file_put_contents($storageDir . '/status.json', json_encode([
                'status' => 'success',
                'stage' => 'complete'
            ], JSON_PRETTY_PRINT));

            // We return EngineResult with the free document as the default for the immediate response
            return new EngineResult($freeDocument, $freeHtml, $metadata);

        } catch (EngineException $e) {
            self::logFailure($storageDir ?? '', $e->getErrorCode(), $e->getMessage(), $e);
            throw $e;
        } catch (Throwable $e) {
            self::logFailure($storageDir ?? '', 'SYS_001', 'Unexpected exception occurred.', $e);
            throw new EngineException('SYS_001', 'Unexpected system error: ' . $e->getMessage());
        }
    }

    private static function logFailure(string $storageDir, string $errorCode, string $message, Throwable $e): void
    {
        if (!$storageDir) return;
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
