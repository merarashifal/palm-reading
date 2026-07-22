<?php

namespace AIAnalysisEngine\Inference\DTO;

class EvidenceCollection implements \IteratorAggregate, \Countable
{
    /** @var Evidence[] */
    private array $evidence = [];

    public function add(Evidence $evidence): void
    {
        $this->evidence[$evidence->id] = $evidence;
    }

    public function findByFeature(string $feature): array
    {
        return array_filter($this->evidence, fn(Evidence $e) => $e->feature === $feature);
    }

    public function highestConfidence(): ?Evidence
    {
        if (empty($this->evidence)) return null;
        
        $highest = null;
        foreach ($this->evidence as $e) {
            if ($highest === null || $e->aiConfidence > $highest->aiConfidence) {
                $highest = $e;
            }
        }
        return $highest;
    }

    public function averageWeight(): float
    {
        if (empty($this->evidence)) return 0.0;
        
        $total = 0;
        foreach ($this->evidence as $e) {
            $total += $e->weight;
        }
        return $total / count($this->evidence);
    }

    public function all(): array
    {
        return $this->evidence;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->evidence);
    }

    public function count(): int
    {
        return count($this->evidence);
    }
}
