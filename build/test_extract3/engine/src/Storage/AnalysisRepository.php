<?php

namespace AIAnalysisEngine\Storage;

use AIAnalysisEngine\Facade\DTO\EngineResult;

class AnalysisRepository
{
    private $storageBasePath;

    public function __construct($storageBasePath)
    {
        $this->storageBasePath = $storageBasePath;
    }

    public function generateReportId()
    {
        // PPB (Personal Palm Blueprint) - YYYY - Random 6 alphanumeric characters
        $year = date('Y');
        $randomStr = strtoupper(substr(md5(uniqid()), 0, 6));
        return "PPB-{$year}-{$randomStr}";
    }

    public function generateRunId()
    {
        return 'run_' . date('Ymd_His') . '_' . substr(md5(uniqid()), 0, 6);
    }

    public function getStorageDir($reportId)
    {
        $dir = $this->storageBasePath . '/analysis/' . $reportId;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    public function generateShareToken()
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
    }

    public function saveLifecycle(
        $reportId,
        $runId,
        $imagePath,
        array $features,
        array $inferenceResult,
        array $reportModelArray,
        array $metadata,
        $freeHtml,
        $premiumHtml,
        $premiumPdfPath = null
    ) {
        $storageDir = $this->getStorageDir($reportId);

        // 1. Metadata (Immutable)
        $metadata['report_id'] = $reportId;
        $metadata['run_id'] = $runId;
        $metadata['share_token'] = $this->generateShareToken();
        file_put_contents($storageDir . '/metadata.json', json_encode($metadata, JSON_PRETTY_PRINT));

        // 1b. State (Mutable)
        $initialState = [
            'state' => 'free',
            'profile_completed' => false,
            'premium_unlocked' => false,
            'feedback_submitted' => false,
            'pdf_downloaded' => false,
            'share_count' => 0,
            'created_at' => date('c'),
            'updated_at' => date('c')
        ];
        file_put_contents($storageDir . '/state.json', json_encode($initialState, JSON_PRETTY_PRINT));

        // 2. Image
        if (file_exists($imagePath)) {
            copy($imagePath, $storageDir . '/upload.' . pathinfo($imagePath, PATHINFO_EXTENSION));
        }

        // 3. normalized.json
        file_put_contents($storageDir . '/normalized.json', json_encode($features, JSON_PRETTY_PRINT));

        // 4. inference.json
        file_put_contents($storageDir . '/inference.json', json_encode($inferenceResult, JSON_PRETTY_PRINT));

        // 5. report.json
        file_put_contents($storageDir . '/report.json', json_encode($reportModelArray, JSON_PRETTY_PRINT));

        // 6. HTML versions
        file_put_contents($storageDir . '/free.html', $freeHtml);
        file_put_contents($storageDir . '/premium.html', $premiumHtml);
    }

    public function updateMetrics($reportId, array $metrics)
    {
        $storageDir = $this->getStorageDir($reportId);
        file_put_contents($storageDir . '/metrics.json', json_encode($metrics, JSON_PRETTY_PRINT));
    }
}
