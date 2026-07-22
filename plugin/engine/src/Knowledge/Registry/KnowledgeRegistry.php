<?php

namespace AIAnalysisEngine\Knowledge\Registry;

class KnowledgeRegistry {
    private array $manifests = [];
    private array $dictionaries = [];
    private array $features = [];

    public function loadManifest(string $path): array {
        if (isset($this->manifests[$path])) {
            return $this->manifests[$path];
        }

        if (!file_exists($path)) {
            throw new \Exception("Manifest not found: $path");
        }

        $content = file_get_contents($path);
        $manifest = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON in manifest: $path");
        }

        $this->manifests[$path] = $manifest;
        return $manifest;
    }

    public function loadDictionary(string $path): array {
        if (isset($this->dictionaries[$path])) {
            return $this->dictionaries[$path];
        }

        if (!file_exists($path)) {
            throw new \Exception("Dictionary not found: $path");
        }

        $content = file_get_contents($path);
        $dictionary = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON in dictionary: $path");
        }

        $this->dictionaries[$path] = $dictionary;
        return $dictionary;
    }
    public function getDeclaredDictionaries(array $manifest, string $dictionaryBasePath): array
    {
        $dictionaries = [];
        $names = $manifest['dictionaries'] ?? [];

        foreach ($names as $name) {
            $dictionaries[$name] = $this->loadDictionaryEntity($dictionaryBasePath, $name);
        }

        return $dictionaries;
    }

    private function loadDictionaryEntity(string $basePath, string $name): array
    {
        $file = rtrim($basePath, '/') . '/' . $name . '.json';
        if (file_exists($file)) {
            return $this->loadDictionaryFile($file);
        }

        $dir = rtrim($basePath, '/') . '/' . $name;
        if (is_dir($dir)) {
            return $this->loadDictionaryDirectory($dir);
        }

        throw new \Exception("Dictionary not found: $name");
    }

    private function loadDictionaryFile(string $path): array
    {
        return $this->loadDictionary($path);
    }

    private function loadDictionaryDirectory(string $path): array
    {
        $result = [];
        $files = scandir($path);
        
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                $key = pathinfo($file, PATHINFO_FILENAME);
                $result[$key] = $this->loadDictionaryFile($path . '/' . $file);
            }
        }
        
        return $result;
    }
}
