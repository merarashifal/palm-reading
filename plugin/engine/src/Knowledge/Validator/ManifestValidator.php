<?php

namespace AIAnalysisEngine\Knowledge\Validator;

use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Contracts\ValidatorInterface;

class ManifestValidator implements ValidatorInterface {
    public function validate(KnowledgeContext $context): ValidationResult {
        $startTime = microtime(true);
        $startMem = memory_get_usage();

        $manifest = $context->getManifest();
        $errors = [];
        $warnings = [];
        $scores = ['manifest' => 100];
        $statistics = [];

        // Validate required fields
        $required = ['knowledge_pack', 'version', 'analysis_type', 'languages', 'features', 'entry_points'];
        foreach ($required as $field) {
            if (!isset($manifest[$field])) {
                $errors[] = "Missing required field: $field";
                $scores['manifest'] -= 20;
            }
        }

        // Calculate actual features vs manifest features
        $actualFeatures = [];
        $actualRules = 0;
        
        if (isset($manifest['entry_points']) && is_array($manifest['entry_points'])) {
            foreach ($manifest['entry_points'] as $entryPoint) {
                $dirPath = $context->getRootPath() . '/' . $entryPoint;
                if (!is_dir($dirPath)) {
                    $errors[] = "Entry point directory not found: $entryPoint";
                    $scores['manifest'] -= 20;
                    continue;
                }
                
                // Very basic calculation for demonstration
                $files = glob($dirPath . '/*.json');
                if ($files) {
                    $actualRules += count($files);
                }
                
                // Assuming feature directories inside entry points if applicable
                $subdirs = glob($dirPath . '/*', GLOB_ONLYDIR);
                if ($subdirs) {
                    foreach ($subdirs as $subdir) {
                        $actualFeatures[] = basename($subdir);
                        $subFiles = glob($subdir . '/*.json');
                        if ($subFiles) {
                            $actualRules += count($subFiles);
                        }
                    }
                }
            }
        }

        $statistics['calculated_rules'] = $actualRules;
        $statistics['calculated_features'] = count($actualFeatures);

        // Compare if manifest declared expected rule count (e.g. in a statistics object)
        if (isset($manifest['statistics']['rules'])) {
            $expectedRules = $manifest['statistics']['rules'];
            if ($expectedRules !== $actualRules) {
                $errors[] = "Rule count mismatch. Manifest: $expectedRules, Actual: $actualRules";
                $scores['manifest'] -= 50;
            }
        }

        $scores['manifest'] = max(0, $scores['manifest']);
        $scores['overall'] = $scores['manifest'];
        
        $status = empty($errors) ? ValidationResult::STATUS_PASS : ValidationResult::STATUS_FAIL;
        if (empty($errors) && !empty($warnings)) {
            $status = ValidationResult::STATUS_WARNING;
        }

        $executionTime = microtime(true) - $startTime;
        $memoryUsage = memory_get_usage() - $startMem;

        return new ValidationResult(
            'ManifestValidator',
            $status,
            $scores,
            $errors,
            $warnings,
            $statistics,
            $executionTime,
            $memoryUsage
        );
    }
}
