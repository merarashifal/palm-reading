<?php

namespace AIAnalysisEngine\Knowledge\Validator;

use AIAnalysisEngine\Contracts\ValidatorInterface;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;

class ReferenceValidator implements ValidatorInterface {
    public function validate(KnowledgeContext $context): ValidationResult {
        $startTime = microtime(true);
        $startMem = memory_get_usage();

        $manifest = $context->getManifest();
        $errors = [];
        $warnings = [];
        $scores = ['references' => 100];
        $statistics = ['references_checked' => 0];
        
        $rootPath = rtrim($context->getRootPath(), '/');
        
        // 1. Validate manifest references (entry points)
        if (isset($manifest['entry_points']) && is_array($manifest['entry_points'])) {
            foreach ($manifest['entry_points'] as $entryPoint) {
                $statistics['references_checked']++;
                if (!is_dir($rootPath . '/' . $entryPoint)) {
                    $errors[] = "Manifest entry point missing: $entryPoint";
                    $scores['references'] -= 10;
                }
            }
        }
        
        // 1b. Validate manifest consistency (features exist)
        if (isset($manifest['features']) && is_array($manifest['features'])) {
            foreach ($manifest['features'] as $feature) {
                $statistics['references_checked']++;
                // Find feature directory in any entry point
                $featureFound = false;
                if (isset($manifest['entry_points'])) {
                    foreach ($manifest['entry_points'] as $entryPoint) {
                        if (is_dir($rootPath . '/' . $entryPoint . '/' . $feature)) {
                            $featureFound = true;
                            break;
                        }
                    }
                }
                if (!$featureFound) {
                    $errors[] = "Feature declared in manifest but directory missing: $feature";
                    $scores['references'] -= 10;
                }
            }
        }
        
        // 2. Validate dictionary references
        $dictionaryPath = $context->getConfiguration()['paths']['dictionaries'] ?? '';
        if (!is_dir($dictionaryPath)) {
            $warnings[] = "Dictionary path missing: $dictionaryPath";
            $scores['references'] -= 5;
        }

        // 3. Scan all rules to check internal references
        if (isset($manifest['entry_points']) && is_array($manifest['entry_points'])) {
            foreach ($manifest['entry_points'] as $entryPoint) {
                $dirPath = $rootPath . '/' . $entryPoint;
                if (is_dir($dirPath)) {
                    $this->validateRuleReferences($dirPath, $manifest, $errors, $warnings, $scores, $statistics);
                }
            }
        }

        $scores['references'] = max(0, $scores['references']);
        $scores['overall'] = $scores['references'];
        
        $status = empty($errors) ? ValidationResult::STATUS_PASS : ValidationResult::STATUS_FAIL;
        if (empty($errors) && !empty($warnings)) {
            $status = ValidationResult::STATUS_WARNING;
        }

        $executionTime = microtime(true) - $startTime;
        $memoryUsage = memory_get_usage() - $startMem;

        return new ValidationResult(
            'ReferenceValidator',
            $status,
            $scores,
            $errors,
            $warnings,
            $statistics,
            $executionTime,
            $memoryUsage
        );
    }
    
    private function validateRuleReferences(string $dirPath, array $manifest, array &$errors, array &$warnings, array &$scores, array &$statistics): void {
        $files = glob($dirPath . '/*.json');
        if ($files) {
            foreach ($files as $file) {
                $this->checkReferencesInFile($file, $manifest, $errors, $warnings, $scores, $statistics);
            }
        }

        $subdirs = glob($dirPath . '/*', GLOB_ONLYDIR);
        if ($subdirs) {
            foreach ($subdirs as $subdir) {
                $this->validateRuleReferences($subdir, $manifest, $errors, $warnings, $scores, $statistics);
            }
        }
    }
    
    private function checkReferencesInFile(string $filePath, array $manifest, array &$errors, array &$warnings, array &$scores, array &$statistics): void {
        $content = file_get_contents($filePath);
        $json = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return; // SchemaValidator handles invalid JSON
        }
        
        $filename = basename($filePath);
        $relativePath = str_replace(dirname(dirname(dirname(dirname(__DIR__)))) . '/', '', str_replace('\\', '/', $filePath));
        
        // Check Analysis, Feature, Section properties exist
        $requiredProps = ['analysis', 'feature', 'section'];
        foreach ($requiredProps as $prop) {
            $statistics['references_checked']++;
            if (!isset($json[$prop])) {
                $errors[] = "Missing reference property '$prop' in $relativePath";
                $scores['references'] -= 5;
            }
        }
        
        // Check if the referenced feature exists in the manifest
        if (isset($json['feature'])) {
            $feature = $json['feature'];
            $statistics['references_checked']++;
            $manifestFeatures = $manifest['features'] ?? [];
            if (!in_array($feature, $manifestFeatures)) {
                $errors[] = "ERROR Feature $feature Referenced from $relativePath does not exist in manifest features";
                $scores['references'] -= 10;
            }
        }
    }
}
