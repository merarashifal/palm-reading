<?php

namespace AIAnalysisEngine\Knowledge\Compiler\Rule;

use AIAnalysisEngine\Knowledge\Registry\KnowledgeRegistry;

class RuleLoader
{
    /**
     * Iterates over all entry points defined in the manifest and fetches
     * the raw rule arrays from the KnowledgeRegistry.
     * 
     * @return array[] List of raw rule arrays
     */
    public function load(KnowledgeRegistry $registry, array $manifest, string $rootPath): array
    {
        $rawRules = [];
        $entryPoints = $manifest['entry_points'] ?? [];

        foreach ($entryPoints as $entryPoint) {
            $path = rtrim($rootPath, '/') . '/' . ltrim($entryPoint, '/');
            
            if (is_dir($path)) {
                $rules = $this->loadDirectory($registry, $path);
                foreach ($rules as $ruleList) {
                    // Dictionaries return associative arrays, but rules might be list arrays
                    // We assume each file returns a list of rules
                    foreach ($ruleList as $rule) {
                        $rawRules[] = $rule;
                    }
                }
            } else {
                $rules = $registry->loadDictionary($path);
                foreach ($rules as $rule) {
                    $rawRules[] = $rule;
                }
            }
        }

        return $rawRules;
    }

    private function loadDirectory(KnowledgeRegistry $registry, string $dir): array
    {
        $result = [];
        $files = scandir($dir);
        
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                $result[] = $registry->loadDictionary($dir . '/' . $file);
            }
        }
        
        return $result;
    }
}
