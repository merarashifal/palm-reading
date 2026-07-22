<?php

namespace AIAnalysisEngine\Knowledge\Validator;

use AIAnalysisEngine\Contracts\ValidatorInterface;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;

class LanguageValidator implements ValidatorInterface {
    
    private array $allParagraphs = [];
    private array $stats = [
        'english' => ['total_words' => 0, 'count' => 0],
        'hindi' => ['total_words' => 0, 'count' => 0]
    ];
    
    public function validate(KnowledgeContext $context): ValidationResult {
        $startTime = microtime(true);
        $startMem = memory_get_usage();

        $manifest = $context->getManifest();
        $errors = [];
        $warnings = [];
        $scores = ['language' => 100];
        $statistics = ['rules_processed' => 0];
        
        $rootPath = rtrim($context->getRootPath(), '/');
        
        if (isset($manifest['entry_points']) && is_array($manifest['entry_points'])) {
            foreach ($manifest['entry_points'] as $entryPoint) {
                $dirPath = $rootPath . '/' . $entryPoint;
                if (is_dir($dirPath)) {
                    $this->validateLanguageInDirectory($dirPath, $errors, $warnings, $scores, $statistics);
                }
            }
        }

        // Calculate averages for language statistics
        $langStats = [
            'english' => ['average_words' => $this->stats['english']['count'] > 0 ? round($this->stats['english']['total_words'] / $this->stats['english']['count']) : 0],
            'hindi' => ['average_words' => $this->stats['hindi']['count'] > 0 ? round($this->stats['hindi']['total_words'] / $this->stats['hindi']['count']) : 0],
        ];
        
        // Write language_statistics.json
        $outputDir = $context->getConfiguration()['paths']['output'] ?? ($rootPath . '/generated');
        $reportDir = $outputDir . '/reports/validation';
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0777, true);
        }
        file_put_contents($reportDir . '/language_statistics.json', json_encode($langStats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $scores['language'] = max(0, $scores['language']);
        $scores['overall'] = $scores['language'];
        
        $status = empty($errors) ? ValidationResult::STATUS_PASS : ValidationResult::STATUS_FAIL;
        if (empty($errors) && !empty($warnings)) {
            $status = ValidationResult::STATUS_WARNING;
        }

        $executionTime = microtime(true) - $startTime;
        $memoryUsage = memory_get_usage() - $startMem;
        
        // Benchmark Check
        $targetMsPerRule = 2; // 2 seconds per 1000 rules = 2ms per rule
        if ($statistics['rules_processed'] > 0) {
            $actualMsPerRule = ($executionTime * 1000) / $statistics['rules_processed'];
            if ($actualMsPerRule > $targetMsPerRule) {
                $warnings[] = "Performance warning: validation took {$actualMsPerRule}ms per rule (target < {$targetMsPerRule}ms)";
            }
        }

        return new ValidationResult(
            'LanguageValidator',
            $status,
            $scores,
            $errors,
            $warnings,
            array_merge($statistics, $langStats),
            $executionTime,
            $memoryUsage
        );
    }
    
    private function validateLanguageInDirectory(string $dirPath, array &$errors, array &$warnings, array &$scores, array &$statistics): void {
        $files = glob($dirPath . '/*.json');
        if ($files) {
            foreach ($files as $file) {
                $this->checkLanguageInFile($file, $errors, $warnings, $scores, $statistics);
            }
        }

        $subdirs = glob($dirPath . '/*', GLOB_ONLYDIR);
        if ($subdirs) {
            foreach ($subdirs as $subdir) {
                $this->validateLanguageInDirectory($subdir, $errors, $warnings, $scores, $statistics);
            }
        }
    }
    
    private function checkLanguageInFile(string $filePath, array &$errors, array &$warnings, array &$scores, array &$statistics): void {
        $content = file_get_contents($filePath);
        $json = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($json['content'])) {
            return;
        }
        
        $statistics['rules_processed']++;
        $filename = basename($filePath);
        $tier = $json['metadata']['tier'] ?? 3;

        // Check required languages
        if (!isset($json['content']['en'])) {
            $errors[] = "Missing English translation in $filename";
            $scores['language'] -= 10;
        }
        if (!isset($json['content']['hi'])) {
            $errors[] = "Missing Hindi translation in $filename";
            $scores['language'] -= 10;
        }

        $enText = $json['content']['en'] ?? '';
        $hiText = $json['content']['hi'] ?? '';
        
        // Check Empty Translations
        if (trim($enText) === '') {
            $errors[] = "Empty English translation in $filename";
            $scores['language'] -= 5;
        }
        if (trim($hiText) === '') {
            $errors[] = "Empty Hindi translation in $filename";
            $scores['language'] -= 5;
        }
        
        // Check UTF-8
        if (!mb_check_encoding($enText, 'UTF-8') || !mb_check_encoding($hiText, 'UTF-8')) {
            $errors[] = "Invalid UTF-8 encoding in translation in $filename";
            $scores['language'] -= 10;
        }
        
        // Check HTML
        if ($enText !== strip_tags($enText) || $hiText !== strip_tags($hiText)) {
            $errors[] = "Illegal HTML tags found in translation in $filename";
            $scores['language'] -= 10;
        }
        
        // Placeholders
        $placeholders = ['lorem ipsum', 'todo', 'xxx', 'coming soon'];
        foreach ([$enText, $hiText] as $text) {
            $lowerText = strtolower($text);
            foreach ($placeholders as $ph) {
                if (strpos($lowerText, $ph) !== false) {
                    $errors[] = "Placeholder '$ph' found in translation in $filename";
                    $scores['language'] -= 5;
                }
            }
        }
        
        // Word count & Tier checks
        $enWordCount = str_word_count($enText);
        $hiWordCount = count(explode(' ', trim(preg_replace('/\s+/', ' ', $hiText)))); // str_word_count doesn't work well for Hindi
        
        if ($enWordCount > 0) {
            $this->stats['english']['total_words'] += $enWordCount;
            $this->stats['english']['count']++;
        }
        if ($hiWordCount > 0) {
            $this->stats['hindi']['total_words'] += $hiWordCount;
            $this->stats['hindi']['count']++;
        }

        // Tier validation
        $targetMin = 0;
        $targetMax = 999;
        if ($tier == 1) { $targetMin = 40; $targetMax = 45; }
        elseif ($tier == 2) { $targetMin = 35; $targetMax = 40; }
        elseif ($tier == 3) { $targetMin = 25; $targetMax = 30; }

        if ($enWordCount > 0 && ($enWordCount < $targetMin || $enWordCount > $targetMax)) {
            $warnings[] = "English word count ($enWordCount) out of bounds for Tier $tier in $filename";
            $scores['language'] -= 2;
        }
        
        // Length Ratio
        if ($enWordCount > 0 && $hiWordCount > 0) {
            $ratio = $enWordCount / $hiWordCount;
            if ($ratio > 2.0 || $ratio < 0.5) {
                $warnings[] = "Length ratio warning: English ($enWordCount words) vs Hindi ($hiWordCount words) in $filename";
                $scores['language'] -= 2;
            }
        }
        
        // Duplicate paragraphs across sections
        // Normalize text by removing punctuation and lowercasing for similarity check
        $normEn = strtolower(preg_replace('/[^a-z0-9\s]/', '', $enText));
        if ($normEn !== '') {
            $foundMatch = false;
            foreach ($this->allParagraphs as $prevFile => $prevNormEn) {
                // simple similarity: if 95% same
                similar_text($normEn, $prevNormEn, $percent);
                if ($percent > 95) {
                    $warnings[] = "Duplicate paragraph detected between $filename and $prevFile (similarity: " . round($percent) . "%)";
                    $scores['language'] -= 5;
                    $foundMatch = true;
                    break;
                }
            }
            if (!$foundMatch) {
                $this->allParagraphs[$filename] = $normEn;
            }
        }
    }
}
