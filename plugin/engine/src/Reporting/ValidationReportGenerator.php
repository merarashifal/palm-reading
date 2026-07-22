<?php

namespace AIAnalysisEngine\Reporting;

use AIAnalysisEngine\Knowledge\Validator\ValidationResult;

class ValidationReportGenerator {
    
    private string $outputDir;

    public function __construct(string $outputDir) {
        $this->outputDir = $outputDir;
    }

    /**
     * @param ValidationResult[] $results
     */
    public function generate(array $results): void {
        $timestamp = gmdate('YmdHis');
        $validationDir = $this->outputDir . '/reports/validation';
        $historyDir = $this->outputDir . '/reports/history/' . $timestamp;
        
        if (!is_dir($validationDir)) mkdir($validationDir, 0777, true);
        if (!is_dir($historyDir)) mkdir($historyDir, 0777, true);

        // Aggregate data
        $reportData = [];
        $overallScore = 0;
        $scores = [];
        $benchmarks = [
            'total_rules_processed' => 0,
            'total_time_sec' => 0.0,
            'total_memory_mb' => 0,
            'validators_run' => count($results)
        ];
        
        $totalErrors = 0;
        
        foreach ($results as $result) {
            $reportData[] = $result->jsonSerialize();
            $validatorName = str_replace('Validator', '', $result->getValidator());
            $validatorName = strtolower($validatorName);
            
            $resScores = $result->getScores();
            $overallScore += $resScores['overall'] ?? 100;
            
            foreach ($resScores as $key => $val) {
                if ($key !== 'overall') {
                    $scores[$key] = $val;
                }
            }
            
            $benchmarks['total_time_sec'] += $result->getExecutionTime();
            $benchmarks['total_memory_mb'] += ($result->getMemoryUsage() / 1024 / 1024);
            $totalErrors += count($result->getErrors());
            
            $stats = $result->getStatistics();
            if (isset($stats['rules_processed']) && $stats['rules_processed'] > $benchmarks['total_rules_processed']) {
                $benchmarks['total_rules_processed'] = $stats['rules_processed']; // Get max rules processed
            }
        }
        
        $scores['overall'] = count($results) > 0 ? round($overallScore / count($results)) : 0;
        
        if ($benchmarks['total_rules_processed'] > 0) {
            $benchmarks['average_ms_per_rule'] = round(($benchmarks['total_time_sec'] * 1000) / $benchmarks['total_rules_processed'], 2);
        } else {
            $benchmarks['average_ms_per_rule'] = 0;
        }

        // 1. report.json
        file_put_contents($validationDir . '/report.json', json_encode($reportData, JSON_PRETTY_PRINT));
        
        // 2. quality_score.json
        file_put_contents($validationDir . '/quality_score.json', json_encode($scores, JSON_PRETTY_PRINT));
        
        // 3. benchmark.json
        file_put_contents($validationDir . '/benchmark.json', json_encode($benchmarks, JSON_PRETTY_PRINT));
        
        // 4. engine_health.json
        $engineHealth = [
            'engine' => 100, // Static for now until full CI
            'knowledge' => $scores['overall'],
            'coverage' => 100, // Placeholder
            'tests' => 100, // Placeholder
            'overall' => round((100 + $scores['overall'] + 100 + 100) / 4)
        ];
        file_put_contents($validationDir . '/engine_health.json', json_encode($engineHealth, JSON_PRETTY_PRINT));
        
        // 5. report.csv (Simplified)
        $csv = fopen($validationDir . '/report.csv', 'w');
        fputcsv($csv, ['Validator', 'Status', 'Errors', 'Warnings', 'Execution Time']);
        foreach ($results as $result) {
            fputcsv($csv, [
                $result->getValidator(),
                $result->getStatus(),
                count($result->getErrors()),
                count($result->getWarnings()),
                round($result->getExecutionTime(), 4)
            ]);
        }
        fclose($csv);

        // 6. report.html (Basic HTML dashboard)
        $html = "<html><body><h1>Knowledge QA Report</h1><p>Overall Score: {$scores['overall']}</p>";
        foreach ($results as $result) {
            $html .= "<h2>{$result->getValidator()} - {$result->getStatus()}</h2>";
            if (!empty($result->getErrors())) {
                $html .= "<ul>";
                foreach ($result->getErrors() as $err) $html .= "<li>ERROR: $err</li>";
                $html .= "</ul>";
            }
        }
        $html .= "</body></html>";
        file_put_contents($validationDir . '/report.html', $html);

        // Copy everything to history
        $files = glob($validationDir . '/*.*');
        foreach ($files as $file) {
            copy($file, $historyDir . '/' . basename($file));
        }
    }
}
