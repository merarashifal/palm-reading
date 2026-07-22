<?php

namespace AIAnalysisEngine\Knowledge\Validator\Editorial;

use AIAnalysisEngine\Contracts\ValidatorInterface;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Validator\ValidationResult;
use AIAnalysisEngine\Knowledge\Registry\KnowledgeRegistry;

class EditorialConsistencyValidator implements ValidatorInterface {
    public function validate(KnowledgeContext $context): ValidationResult {
        $startTime = microtime(true);
        $startMem = memory_get_usage();

        $manifest = $context->getManifest();
        $errors = [];
        $warnings = [];
        $scores = ['consistency' => 100];
        $statistics = ['rules_processed' => 0];
        
        $rootPath = rtrim($context->getRootPath(), '/');
        
        if (isset($manifest['entry_points']) && is_array($manifest['entry_points'])) {
            foreach ($manifest['entry_points'] as $entryPoint) {
                $dirPath = $rootPath . '/' . $entryPoint;
                if (is_dir($dirPath)) {
                    $this->validateDirectory($dirPath, $context, $errors, $warnings, $scores, $statistics);
                }
            }
        }

        $scores['consistency'] = max(0, $scores['consistency']);
        $scores['overall'] = $scores['consistency'];
        
        $status = empty($errors) ? ValidationResult::STATUS_PASS : ValidationResult::STATUS_FAIL;
        if (empty($errors) && !empty($warnings)) {
            $status = ValidationResult::STATUS_WARNING;
        }

        return new ValidationResult(
            'EditorialConsistencyValidator',
            $status,
            $scores,
            $errors,
            $warnings,
            $statistics,
            microtime(true) - $startTime,
            memory_get_usage() - $startMem
        );
    }
    
    private function validateDirectory(string $dirPath, KnowledgeContext $context, array &$errors, array &$warnings, array &$scores, array &$statistics): void {
        $files = glob($dirPath . '/*.json');
        if ($files) {
            foreach ($files as $file) {
                $this->checkFile($file, $context, $errors, $warnings, $scores, $statistics);
            }
        }
        $subdirs = glob($dirPath . '/*', GLOB_ONLYDIR);
        if ($subdirs) {
            foreach ($subdirs as $subdir) {
                $this->validateDirectory($subdir, $context, $errors, $warnings, $scores, $statistics);
            }
        }
    }
    
    private function checkFile(string $filePath, KnowledgeContext $context, array &$errors, array &$warnings, array &$scores, array &$statistics): void {
        $content = file_get_contents($filePath);
        $json = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($json['content']['en'])) return;
        
        $statistics['rules_processed']++;
        $filename = basename($filePath);
        
        $tier = $json['metadata']['tier'] ?? 3;
        $registry = new KnowledgeRegistry();
        $dictPath = $context->getConfiguration()['paths']['dictionaries'] ?? '';
        
        $tierDictFile = $dictPath . "/editorial/tier{$tier}.json";
        if (!file_exists($tierDictFile)) return;
        
        $tierRules = $registry->loadDictionary($tierDictFile);
        $enText = strtolower($json['content']['en']);
        
        // This is a simplified check for required sections (based on heuristics for testing)
        // A real check would parse the JSON into Observation, Interpretation, etc. if structured.
        $hasInterpretation = strpos($enText, 'mean') !== false || strpos($enText, 'indicate') !== false;
        
        if (in_array('interpretation', $tierRules['required_sections'] ?? []) && !$hasInterpretation) {
            $errors[] = "Tier $tier rule missing required interpretation in $filename";
            $scores['consistency'] -= 10;
        }
    }
}
