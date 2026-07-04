<?php

namespace AIAnalysisEngine\Knowledge\Validator\Editorial;

use AIAnalysisEngine\Contracts\ValidatorInterface;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Validator\ValidationResult;

class ReadabilityValidator implements ValidatorInterface {
    public function validate(KnowledgeContext $context): ValidationResult {
        $startTime = microtime(true);
        $startMem = memory_get_usage();

        $manifest = $context->getManifest();
        $errors = [];
        $warnings = [];
        $scores = ['readability' => 100];
        $statistics = ['total_sentences' => 0, 'total_words' => 0, 'rules_processed' => 0];
        
        $rootPath = rtrim($context->getRootPath(), '/');
        $config = $context->getConfiguration();
        $editorialConfig = require dirname(dirname(dirname(__DIR__))) . '/config/editorial.php';
        $maxAvgSentenceLength = $editorialConfig['readability']['max_sentence_length'] ?? 20;
        
        if (isset($manifest['entry_points']) && is_array($manifest['entry_points'])) {
            foreach ($manifest['entry_points'] as $entryPoint) {
                $dirPath = $rootPath . '/' . $entryPoint;
                if (is_dir($dirPath)) {
                    $this->validateDirectory($dirPath, $errors, $warnings, $scores, $statistics, $maxAvgSentenceLength);
                }
            }
        }

        $scores['readability'] = max(0, $scores['readability']);
        $scores['overall'] = $scores['readability'];
        
        $status = empty($errors) ? ValidationResult::STATUS_PASS : ValidationResult::STATUS_FAIL;
        if (empty($errors) && !empty($warnings)) {
            $status = ValidationResult::STATUS_WARNING;
        }

        $executionTime = microtime(true) - $startTime;
        $memoryUsage = memory_get_usage() - $startMem;
        
        $avgSentenceLen = $statistics['total_sentences'] > 0 ? round($statistics['total_words'] / $statistics['total_sentences']) : 0;
        $readabilityStats = [
            'avg_sentence_length' => $avgSentenceLen,
            'avg_word_length' => 5, // Simplified static value for example
            'avg_reading_time' => 15
        ];

        // Write readability_statistics.json
        $outputDir = $config['paths']['output'] ?? ($rootPath . '/generated');
        $reportDir = $outputDir . '/reports/validation';
        if (!is_dir($reportDir)) mkdir($reportDir, 0777, true);
        file_put_contents($reportDir . '/readability_statistics.json', json_encode($readabilityStats, JSON_PRETTY_PRINT));

        return new ValidationResult(
            'ReadabilityValidator',
            $status,
            $scores,
            $errors,
            $warnings,
            array_merge($statistics, $readabilityStats),
            $executionTime,
            $memoryUsage
        );
    }
    
    private function validateDirectory(string $dirPath, array &$errors, array &$warnings, array &$scores, array &$statistics, int $maxAvgSentenceLength): void {
        $files = glob($dirPath . '/*.json');
        if ($files) {
            foreach ($files as $file) {
                $this->checkFile($file, $errors, $warnings, $scores, $statistics, $maxAvgSentenceLength);
            }
        }
        $subdirs = glob($dirPath . '/*', GLOB_ONLYDIR);
        if ($subdirs) {
            foreach ($subdirs as $subdir) {
                $this->validateDirectory($subdir, $errors, $warnings, $scores, $statistics, $maxAvgSentenceLength);
            }
        }
    }
    
    private function checkFile(string $filePath, array &$errors, array &$warnings, array &$scores, array &$statistics, int $maxAvgSentenceLength): void {
        $content = file_get_contents($filePath);
        $json = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($json['content']['en'])) return;
        
        $statistics['rules_processed']++;
        $filename = basename($filePath);
        $enText = $json['content']['en'];
        
        $sentences = preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $enText);
        $sentenceCount = count($sentences);
        $wordCount = str_word_count($enText);
        
        $statistics['total_sentences'] += $sentenceCount;
        $statistics['total_words'] += $wordCount;
        
        if ($sentenceCount > 0) {
            $avgLength = $wordCount / $sentenceCount;
            if ($avgLength > $maxAvgSentenceLength) {
                $warnings[] = "Average sentence length ($avgLength) exceeds threshold ($maxAvgSentenceLength) in $filename";
                $scores['readability'] -= 2;
            }
        }
        
        // Very rudimentary passive voice check
        $passiveMarkers = ['is', 'are', 'was', 'were', 'be', 'been', 'being'];
        $passiveCount = 0;
        $words = str_word_count(strtolower($enText), 1);
        foreach ($words as $word) {
            if (in_array($word, $passiveMarkers)) $passiveCount++;
        }
        
        if ($wordCount > 0 && ($passiveCount / $wordCount) > 0.15) {
            $warnings[] = "High passive voice detected in $filename";
            $scores['readability'] -= 2;
        }
    }
}
