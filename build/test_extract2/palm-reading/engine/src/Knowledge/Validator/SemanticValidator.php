<?php

namespace AIAnalysisEngine\Knowledge\Validator;

use AIAnalysisEngine\Contracts\ValidatorInterface;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Registry\KnowledgeRegistry;

class SemanticValidator implements ValidatorInterface {
    
    public function validate(KnowledgeContext $context): ValidationResult {
        $startTime = microtime(true);
        $startMem = memory_get_usage();

        $manifest = $context->getManifest();
        $errors = [];
        $warnings = [];
        $scores = ['semantic' => 100];
        $statistics = ['rules_processed' => 0];
        
        $rootPath = rtrim($context->getRootPath(), '/');
        $registry = new KnowledgeRegistry();
        $dictPath = $context->getConfiguration()['paths']['dictionaries'] ?? '';
        $semanticDictPath = $dictPath . '/semantic';
        
        if (isset($manifest['entry_points']) && is_array($manifest['entry_points'])) {
            foreach ($manifest['entry_points'] as $entryPoint) {
                $dirPath = $rootPath . '/' . $entryPoint;
                if (is_dir($dirPath)) {
                    $this->validateDirectory($dirPath, $semanticDictPath, $registry, $errors, $warnings, $scores, $statistics);
                }
            }
        }

        $scores['semantic'] = max(0, $scores['semantic']);
        $scores['overall'] = $scores['semantic'];
        
        $status = empty($errors) ? ValidationResult::STATUS_PASS : ValidationResult::STATUS_FAIL;
        if (empty($errors) && !empty($warnings)) {
            $status = ValidationResult::STATUS_WARNING;
        }

        return new ValidationResult(
            'SemanticValidator',
            $status,
            $scores,
            $errors,
            $warnings,
            $statistics,
            microtime(true) - $startTime,
            memory_get_usage() - $startMem
        );
    }
    
    private function validateDirectory(string $dirPath, string $semanticDictPath, KnowledgeRegistry $registry, array &$errors, array &$warnings, array &$scores, array &$statistics): void {
        $files = glob($dirPath . '/*.json');
        if ($files) {
            foreach ($files as $file) {
                $this->checkFile($file, $semanticDictPath, $registry, $errors, $warnings, $scores, $statistics);
            }
        }
        $subdirs = glob($dirPath . '/*', GLOB_ONLYDIR);
        if ($subdirs) {
            foreach ($subdirs as $subdir) {
                $this->validateDirectory($subdir, $semanticDictPath, $registry, $errors, $warnings, $scores, $statistics);
            }
        }
    }
    
    private function checkFile(string $filePath, string $semanticDictPath, KnowledgeRegistry $registry, array &$errors, array &$warnings, array &$scores, array &$statistics): void {
        $content = file_get_contents($filePath);
        $json = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($json['content']['en'])) return;
        
        $statistics['rules_processed']++;
        $filename = basename($filePath);
        $enText = strtolower($json['content']['en']);
        $section = $json['section'] ?? '';
        
        if ($section) {
            $dictFile = $semanticDictPath . '/' . $section . '.json';
            if (file_exists($dictFile)) {
                $dictionary = $registry->loadDictionary($dictFile);
                
                // Check required words (At least ONE must exist)
                if (isset($dictionary['required']) && is_array($dictionary['required'])) {
                    $foundRequired = false;
                    foreach ($dictionary['required'] as $word) {
                        if (strpos($enText, $word) !== false) {
                            $foundRequired = true;
                            break;
                        }
                    }
                    if (!$foundRequired && !empty($dictionary['required'])) {
                        $warnings[] = "Semantic warning: $filename (section '$section') is missing core contextual keywords.";
                        $scores['semantic'] -= 5;
                    }
                }
                
                // Check forbidden words
                if (isset($dictionary['forbidden']) && is_array($dictionary['forbidden'])) {
                    foreach ($dictionary['forbidden'] as $word) {
                        if (strpos($enText, $word) !== false) {
                            $errors[] = "Semantic error: $filename (section '$section') contains forbidden keyword '$word'.";
                            $scores['semantic'] -= 10;
                        }
                    }
                }
            }
        }
    }
}
