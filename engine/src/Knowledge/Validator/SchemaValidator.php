<?php

namespace AIAnalysisEngine\Knowledge\Validator;

use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Contracts\ValidatorInterface;

class SchemaValidator implements ValidatorInterface {
    public function validate(KnowledgeContext $context): ValidationResult {
        $startTime = microtime(true);
        $startMem = memory_get_usage();

        $manifest = $context->getManifest();
        $errors = [];
        $warnings = [];
        $scores = ['schema' => 100];
        $statistics = ['files_checked' => 0];

        $schemaPath = $context->getConfiguration()['paths']['schema'] ?? '';
        
        if (!file_exists($schemaPath)) {
            $errors[] = "Schema definition not found at: $schemaPath";
            $scores['schema'] = 0;
        }

        if (isset($manifest['entry_points']) && is_array($manifest['entry_points'])) {
            foreach ($manifest['entry_points'] as $entryPoint) {
                $dirPath = $context->getRootPath() . '/' . $entryPoint;
                if (is_dir($dirPath)) {
                    $this->validateDirectory($dirPath, $errors, $warnings, $scores, $statistics);
                }
            }
        }

        $scores['schema'] = max(0, $scores['schema']);
        $scores['overall'] = $scores['schema'];
        
        $status = empty($errors) ? ValidationResult::STATUS_PASS : ValidationResult::STATUS_FAIL;
        if (empty($errors) && !empty($warnings)) {
            $status = ValidationResult::STATUS_WARNING;
        }

        $executionTime = microtime(true) - $startTime;
        $memoryUsage = memory_get_usage() - $startMem;

        return new ValidationResult(
            'SchemaValidator',
            $status,
            $scores,
            $errors,
            $warnings,
            $statistics,
            $executionTime,
            $memoryUsage
        );
    }

    private function validateDirectory(string $dirPath, array &$errors, array &$warnings, array &$scores, array &$statistics): void {
        $files = glob($dirPath . '/*.json');
        if ($files) {
            foreach ($files as $file) {
                $this->validateFile($file, $errors, $warnings, $scores, $statistics);
            }
        }

        $subdirs = glob($dirPath . '/*', GLOB_ONLYDIR);
        if ($subdirs) {
            foreach ($subdirs as $subdir) {
                $this->validateDirectory($subdir, $errors, $warnings, $scores, $statistics);
            }
        }
    }

    private function validateFile(string $filePath, array &$errors, array &$warnings, array &$scores, array &$statistics): void {
        $statistics['files_checked']++;
        $filename = basename($filePath);
        
        // Check Extension
        if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'json') {
            $errors[] = "Invalid extension for file: $filename";
            $scores['schema'] -= 5;
            return;
        }

        // Check Filename format (alphanumeric and underscores)
        if (!preg_match('/^[a-z0-9_]+\.json$/', $filename)) {
            $warnings[] = "Filename contains non-standard characters: $filename";
            $scores['schema'] -= 1;
        }

        $content = file_get_contents($filePath);
        
        // Check Encoding
        if (!mb_check_encoding($content, 'UTF-8')) {
            $errors[] = "File is not UTF-8 encoded: $filename";
            $scores['schema'] -= 10;
            return;
        }

        // Check Illegal characters (e.g. smart quotes or control chars)
        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $content)) {
            $errors[] = "File contains illegal control characters: $filename";
            $scores['schema'] -= 10;
        }

        $json = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $errors[] = "Invalid JSON in file $filename: " . json_last_error_msg();
            $scores['schema'] -= 20;
            return;
        }

        // Check UID format
        if (isset($json['uid'])) {
            if (!preg_match('/^[A-Z0-9_]+$/', $json['uid'])) {
                $errors[] = "Invalid UID format in $filename: {$json['uid']}";
                $scores['schema'] -= 5;
            }
        }

        // Check UUID format if present
        if (isset($json['uuid'])) {
            if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $json['uuid'])) {
                $errors[] = "Invalid UUID format in $filename: {$json['uuid']}";
                $scores['schema'] -= 5;
            }
        }

        // Check Reserved Keywords (e.g., using "id" instead of "uid", etc. depending on schema)
        // This is a placeholder for specific reserved keyword checks
        $reserved = ['internal_id', '__v'];
        foreach ($reserved as $res) {
            if (array_key_exists($res, $json)) {
                $errors[] = "Reserved keyword '$res' found in $filename";
                $scores['schema'] -= 5;
            }
        }
    }
}
