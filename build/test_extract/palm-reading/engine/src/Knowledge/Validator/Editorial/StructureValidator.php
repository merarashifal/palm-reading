<?php

namespace AIAnalysisEngine\Knowledge\Validator\Editorial;

use AIAnalysisEngine\Contracts\ValidatorInterface;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Validator\ValidationResult;

class StructureValidator implements ValidatorInterface {
    public function validate(KnowledgeContext $context): ValidationResult {
        $startTime = microtime(true);
        $startMem = memory_get_usage();

        $manifest = $context->getManifest();
        $errors = [];
        $warnings = [];
        $scores = ['structure' => 100];
        $statistics = ['rules_processed' => 0];
        
        $rootPath = rtrim($context->getRootPath(), '/');
        
        if (isset($manifest['entry_points']) && is_array($manifest['entry_points'])) {
            foreach ($manifest['entry_points'] as $entryPoint) {
                $dirPath = $rootPath . '/' . $entryPoint;
                if (is_dir($dirPath)) {
                    $this->validateDirectory($dirPath, $errors, $warnings, $scores, $statistics);
                }
            }
        }

        $scores['structure'] = max(0, $scores['structure']);
        $scores['overall'] = $scores['structure'];
        
        $status = empty($errors) ? ValidationResult::STATUS_PASS : ValidationResult::STATUS_FAIL;
        if (empty($errors) && !empty($warnings)) {
            $status = ValidationResult::STATUS_WARNING;
        }

        return new ValidationResult(
            'StructureValidator',
            $status,
            $scores,
            $errors,
            $warnings,
            $statistics,
            microtime(true) - $startTime,
            memory_get_usage() - $startMem
        );
    }
    
    private function validateDirectory(string $dirPath, array &$errors, array &$warnings, array &$scores, array &$statistics): void {
        $files = glob($dirPath . '/*.json');
        if ($files) {
            foreach ($files as $file) {
                $this->checkFile($file, $errors, $warnings, $scores, $statistics);
            }
        }
        $subdirs = glob($dirPath . '/*', GLOB_ONLYDIR);
        if ($subdirs) {
            foreach ($subdirs as $subdir) {
                $this->validateDirectory($subdir, $errors, $warnings, $scores, $statistics);
            }
        }
    }
    
    private function checkFile(string $filePath, array &$errors, array &$warnings, array &$scores, array &$statistics): void {
        $content = file_get_contents($filePath);
        $json = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($json['content']['en'])) return;
        
        $statistics['rules_processed']++;
        $filename = basename($filePath);
        $enText = $json['content']['en'];
        
        // Heuristic: Check if Observation, Interpretation, Curiosity sequence is present.
        // Assuming Observation ends with a period, Interpretation is the middle, Curiosity ends with a question mark.
        // For actual robust checks, maybe the JSON has an internal structure representation, but if it's raw text:
        
        $hasQuestion = strpos($enText, '?') !== false;
        
        // Simple heuristic for this assignment:
        if (strlen($enText) < 50) {
            $errors[] = "Missing observation or interpretation in $filename";
            $scores['structure'] -= 20;
        }
        
        if (!$hasQuestion) {
            $errors[] = "Missing curiosity section in $filename (No question mark found)";
            $scores['structure'] -= 20;
        }
    }
}
