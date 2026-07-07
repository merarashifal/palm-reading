<?php

namespace AIAnalysisEngine\Inference\DTO;

class FeatureCollection implements \IteratorAggregate, \Countable
{
    /** @var Feature[] */
    private array $features = [];

    public function add(Feature $feature): void
    {
        $this->features[$feature->id] = $feature;
    }

    public function get(string $id): ?Feature
    {
        return $this->features[$id] ?? null;
    }

    public function all(): array
    {
        return $this->features;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->features);
    }

    public function count(): int
    {
        return count($this->features);
    }
}
