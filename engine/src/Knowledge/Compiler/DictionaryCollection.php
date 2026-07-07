<?php

namespace AIAnalysisEngine\Knowledge\Compiler;

final class DictionaryCollection
{
    private array $dictionaries = [];

    public function add(string $name, array $dictionary): void
    {
        $this->dictionaries[$name] = $dictionary;
    }

    public function get(string $name): array
    {
        return $this->dictionaries[$name] ?? [];
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->dictionaries);
    }

    public function all(): array
    {
        return $this->dictionaries;
    }

    public function count(): int
    {
        return count($this->dictionaries);
    }

    public function names(): array
    {
        return array_keys($this->dictionaries);
    }
}
