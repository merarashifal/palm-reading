<?php

namespace AIAnalysisEngine\Knowledge\Validator\Editorial;

use AIAnalysisEngine\Contracts\ValidatorInterface;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Validator\ValidationResult;
use AIAnalysisEngine\Knowledge\Registry\KnowledgeRegistry;

class PhraseValidator implements ValidatorInterface {
    
    private array $openingCounts = [];
    private array $curiosityCounts = [];
    
    public function validate(KnowledgeContext $context): ValidationResult {
        $startTime = microtime(true);
        $startMem = memory_get_usage();

        $manifest = $context->getManifest();
        $errors = [];
        $warnings = [];
        $scores = ['phrases' => 100];
        $statistics = ['rules_processed' => 0];
        
        $rootPath = rtrim($context->getRootPath(), '/');
        $registry = new KnowledgeRegistry();
        $dictPath = $context->getConfiguration()['paths']['dictionaries'] ?? '';
        
        $openingsDict = [];
        if (file_exists($dictPath . '/openings.json')) {
            $openingsDict = $registry->loadDictionary($dictPath . '/openings.json');
        }
        
        if (isset($manifest['entry_points']) && is_array($manifest['entry_points'])) {
            foreach ($manifest['entry_points'] as $entryPoint) {
                $dirPath = $rootPath . '/' . $entryPoint;
                if (is_dir($dirPath)) {
                    $this->validateDirectory($dirPath, $errors, $warnings, $scores, $statistics);
                }
            }
        }

        // Validate dictionary usage (ensure all 5 openings are used)
        if (!empty($openingsDict)) {
            $unusedOpenings = 0;
            // Simplified check: compare count of unique openings used vs dictionary size
            // Realistically we'd match exact strings
            if (count($this->openingCounts) < count($openingsDict)) {
                $unusedOpenings = count($openingsDict) - count($this->openingCounts);
                $warnings[] = "$unusedOpenings dictionary openings are never used in the pack.";
                $scores['phrases'] -= 10;
            }
        }

        // Phrase repetition check
        foreach ($this->openingCounts as $phrase => $count) {
            if ($statistics['rules_processed'] > 0 && ($count / $statistics['rules_processed']) > 0.5) {
                $warnings[] = "Opening phrase '$phrase' is used in more than 50% of the rules.";
                $scores['phrases'] -= 5;
            }
        }

        $scores['phrases'] = max(0, $scores['phrases']);
        $scores['overall'] = $scores['phrases'];
        
        $status = empty($errors) ? ValidationResult::STATUS_PASS : ValidationResult::STATUS_FAIL;
        if (empty($errors) && !empty($warnings)) {
            $status = ValidationResult::STATUS_WARNING;
        }

        return new ValidationResult(
            'PhraseValidator',
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
        $enText = $json['content']['en'];
        
        // Simple extraction of first 4 words as "opening phrase"
        $words = explode(' ', $enText);
        $opening = implode(' ', array_slice($words, 0, 4));
        if (!isset($this->openingCounts[$opening])) $this->openingCounts[$opening] = 0;
        $this->openingCounts[$opening]++;
        
        // Simple extraction of last 4 words as "curiosity phrase"
        $curiosity = implode(' ', array_slice($words, -4));
        if (!isset($this->curiosityCounts[$curiosity])) $this->curiosityCounts[$curiosity] = 0;
        $this->curiosityCounts[$curiosity]++;
    }
}
