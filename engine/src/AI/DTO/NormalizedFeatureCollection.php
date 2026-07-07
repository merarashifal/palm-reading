<?php

namespace AIAnalysisEngine\AI\DTO;

class NormalizedFeatureCollection implements \IteratorAggregate, \Countable
{
    /** @var NormalizedFeature[] */
    private array $features = [];

    public function add(NormalizedFeature $feature): void
    {
        $this->features[] = $feature;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->features);
    }

    public function count(): int
    {
        return count($this->features);
    }

    public function all(): array
    {
        return $this->features;
    }
}
