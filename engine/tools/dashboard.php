<?php

$storagePath = __DIR__ . '/../storage';
$analysisPath = $storagePath . '/analysis';
$profilesPath = $storagePath . '/profiles';

$totalUploads = 0;
$successfulAnalyses = 0;
$failedAnalyses = 0;
$unlockClicks = 0;
$profilesCompleted = 0;
$pdfDownloads = 0;
$shares = 0;

$totalProcessingTime = 0;
$insightCounts = [];
$failureReasons = [];

// 1. Scan Analyses
if (is_dir($analysisPath)) {
    $dirs = array_filter(glob($analysisPath . '/*'), 'is_dir');
    $totalUploads = count($dirs);

    foreach ($dirs as $dir) {
        // Status
        if (file_exists($dir . '/status.json')) {
            $status = json_decode(file_get_contents($dir . '/status.json'), true);
            if (($status['status'] ?? '') === 'success') {
                $successfulAnalyses++;
            } else {
                $failedAnalyses++;
                $reason = $status['message'] ?? 'Unknown Error';
                $failureReasons[$reason] = ($failureReasons[$reason] ?? 0) + 1;
            }
        }

        // Metrics
        if (file_exists($dir . '/metrics.json')) {
            $metrics = json_decode(file_get_contents($dir . '/metrics.json'), true);
            $totalProcessingTime += ($metrics['latency'] ?? 0);
        }

        // Simulated Events (Would normally be in an events log/DB, but we can mock or read from a metrics file if updated)
        // For the dashboard script, we'll scan an events.log if it exists, or just leave as 0 for now until the frontend starts logging.
    }
}

// 2. Scan Profiles
if (is_dir($profilesPath)) {
    $files = glob($profilesPath . '/*.json');
    foreach ($files as $file) {
        $profile = json_decode(file_get_contents($file), true);
        if (!empty($profile['identity']['name']) && !empty($profile['identity']['mobile'])) {
            $profilesCompleted++;
        }
    }
}

$successRate = $totalUploads > 0 ? round(($successfulAnalyses / $totalUploads) * 100, 1) : 0;
$avgProcessing = $successfulAnalyses > 0 ? round(($totalProcessingTime / $successfulAnalyses) / 1000, 1) : 0;

arsort($failureReasons);
$topFailure = key($failureReasons) ?: 'None';

echo "========================================\n";
echo "           BETA DASHBOARD               \n";
echo "========================================\n\n";

echo str_pad("Uploads:", 25) . $totalUploads . "\n";
echo str_pad("Reports Generated:", 25) . $successfulAnalyses . "\n";
echo str_pad("Unlock Clicks:", 25) . $unlockClicks . " (Needs Event Tracking)\n";
echo str_pad("Profiles Completed:", 25) . $profilesCompleted . "\n";
echo str_pad("PDF Downloads:", 25) . $pdfDownloads . " (Needs Event Tracking)\n";
echo str_pad("Shares:", 25) . $shares . " (Needs Event Tracking)\n\n";

echo str_pad("Average Processing:", 25) . $avgProcessing . " sec\n";
echo str_pad("Success Rate:", 25) . $successRate . "%\n\n";

echo str_pad("Top Insight:", 25) . "Pending (Needs Insights Aggregation)\n";
echo str_pad("Most Failed Stage:", 25) . $topFailure . "\n";
echo "========================================\n";
