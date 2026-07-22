<?php

namespace AIAnalysisEngine\Knowledge\Validator\Editorial;

use AIAnalysisEngine\Contracts\ValidatorInterface;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Validator\ValidationResult;

class ToneValidator implements ValidatorInterface {
    
    private array $negativeWords = ['fear', 'danger', 'loss', 'failure', 'bad', 'terrible', 'warning'];
    
    public function validate(KnowledgeContext $context): ValidationResult {
        $startTime = microtime(true);
        $startMem = memory_get_usage();

        $manifest = $context->getManifest();
        $errors = [];
        $warnings = [];
        $scores = ['tone' => 100];
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

        $scores['tone'] = max(0, $scores['tone']);
        $scores['overall'] = $scores['tone'];
        
        $status = empty($errors) ? ValidationResult::STATUS_PASS : ValidationResult::STATUS_FAIL;
        if (empty($errors) && !empty($warnings)) {
            $status = ValidationResult::STATUS_WARNING;
        }

        return new ValidationResult(
            'ToneValidator',
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
        $enText = strtolower($json['content']['en']);
        $declaredTone = $json['metadata']['tone'] ?? 'neutral';
        
        if ($declaredTone === 'positive') {
            foreach ($this->negativeWords as $word) {
                if (strpos($enText, $word) !== false) {
                    $errors[] = "Tone mismatch in $filename: declared 'positive' but contains negative word '$word'";
                    $scores['tone'] -= 10;
                }
            }
        }
    }
}
