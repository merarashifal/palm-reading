<?php

namespace AIAnalysisEngine\Storage;

use AIAnalysisEngine\Facade\DTO\EngineResult;

class AnalysisRepository
{
    private string $storageBasePath;

    public function __construct(string $storageBasePath)
    {
        $this->storageBasePath = $storageBasePath;
    }

    public function generateReportId(): string
    {
        // PPB (Personal Palm Blueprint) - YYYY - Random 6 alphanumeric characters
        $year = date('Y');
        $randomStr = strtoupper(substr(md5(uniqid()), 0, 6));
        return "PPB-{$year}-{$randomStr}";
    }

    public function generateRunId(): string
    {
        return 'run_' . date('Ymd_His') . '_' . substr(md5(uniqid()), 0, 6);
    }

    public function getStorageDir(string $reportId): string
    {
        $dir = $this->storageBasePath . '/analysis/' . $reportId;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    public function saveLifecycle(
        string $reportId,
        string $runId,
        string $imagePath,
        array $features,
        array $inferenceResult,
        array $reportModelArray,
        array $metadata,
        string $freeHtml,
        string $premiumHtml,
        string $premiumPdfPath = null
    ): void {
        $storageDir = $this->getStorageDir($reportId);

        // 1. Metadata
        $metadata['report_id'] = $reportId;
        $metadata['run_id'] = $runId;
        file_put_contents($storageDir . '/metadata.json', json_encode($metadata, JSON_PRETTY_PRINT));

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

    public function updateMetrics(string $reportId, array $metrics): void
    {
        $storageDir = $this->getStorageDir($reportId);
        file_put_contents($storageDir . '/metrics.json', json_encode($metrics, JSON_PRETTY_PRINT));
    }
}
