<?php

namespace AIAnalysisEngine\Knowledge\Validator\Editorial;

use AIAnalysisEngine\Contracts\ValidatorInterface;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Validator\ValidationResult;

class ReadingTimeValidator implements ValidatorInterface {
    public function validate(KnowledgeContext $context): ValidationResult {
        $startTime = microtime(true);
        $startMem = memory_get_usage();

        $manifest = $context->getManifest();
        $errors = [];
        $warnings = [];
        $scores = ['reading_time' => 100];
        $statistics = ['rules_processed' => 0];
        
        $rootPath = rtrim($context->getRootPath(), '/');
        $editorialConfig = require dirname(dirname(dirname(__DIR__))) . '/config/editorial.php';
        $wpm = $editorialConfig['readability']['average_reading_speed_wpm'] ?? 200;
        
        if (isset($manifest['entry_points']) && is_array($manifest['entry_points'])) {
            foreach ($manifest['entry_points'] as $entryPoint) {
                $dirPath = $rootPath . '/' . $entryPoint;
                if (is_dir($dirPath)) {
                    $this->validateDirectory($dirPath, $errors, $warnings, $scores, $statistics, $wpm);
                }
            }
        }

        $scores['reading_time'] = max(0, $scores['reading_time']);
        $scores['overall'] = $scores['reading_time'];
        
        $status = empty($errors) ? ValidationResult::STATUS_PASS : ValidationResult::STATUS_FAIL;
        if (empty($errors) && !empty($warnings)) {
            $status = ValidationResult::STATUS_WARNING;
        }

        return new ValidationResult(
            'ReadingTimeValidator',
            $status,
            $scores,
            $errors,
            $warnings,
            $statistics,
            microtime(true) - $startTime,
            memory_get_usage() - $startMem
        );
    }
    
    private function validateDirectory(string $dirPath, array &$errors, array &$warnings, array &$scores, array &$statistics, int $wpm): void {
        $files = glob($dirPath . '/*.json');
        if ($files) {
            foreach ($files as $file) {
                $this->checkFile($file, $errors, $warnings, $scores, $statistics, $wpm);
            }
        }
        $subdirs = glob($dirPath . '/*', GLOB_ONLYDIR);
        if ($subdirs) {
            foreach ($subdirs as $subdir) {
                $this->validateDirectory($subdir, $errors, $warnings, $scores, $statistics, $wpm);
            }
        }
    }
    
    private function checkFile(string $filePath, array &$errors, array &$warnings, array &$scores, array &$statistics, int $wpm): void {
        $content = file_get_contents($filePath);
        $json = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($json['content']['en'])) return;
        
        $statistics['rules_processed']++;
        $filename = basename($filePath);
        $enText = $json['content']['en'];
        $storedTimeStr = $json['metadata']['reading_time'] ?? '15 sec';
        
        $storedSec = (int) filter_var($storedTimeStr, FILTER_SANITIZE_NUMBER_INT);
        $wordCount = str_word_count($enText);
        $calculatedSec = round(($wordCount / $wpm) * 60);
        
        // Allow a small delta
        if (abs($calculatedSec - $storedSec) > 3) {
            $warnings[] = "Reading time mismatch in $filename. Stored: {$storedSec}s, Calculated: {$calculatedSec}s";
            $scores['reading_time'] -= 5;
        }
    }
}
