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
}
