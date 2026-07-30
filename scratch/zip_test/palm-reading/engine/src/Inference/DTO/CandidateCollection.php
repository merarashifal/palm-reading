<?php

namespace AIAnalysisEngine\Inference\DTO;

class CandidateCollection implements \IteratorAggregate, \Countable
{
    /** @var Candidate[] */
    private array $candidates = [];

    public function add(Candidate $candidate): void
    {
        $this->candidates[$candidate->id] = $candidate;
    }

    public function active(): array
    {
        return array_filter($this->candidates, fn(Candidate $c) => $c->status === CandidateStatus::ACTIVE);
    }

    public function discarded(): array
    {
        return array_filter($this->candidates, fn(Candidate $c) => $c->status === CandidateStatus::DISCARDED);
    }

    public function merged(): array
    {
        return array_filter($this->candidates, fn(Candidate $c) => $c->status === CandidateStatus::MERGED);
    }

    public function overridden(): array
    {
        return array_filter($this->candidates, fn(Candidate $c) => $c->status === CandidateStatus::OVERRIDDEN);
    }

    public function countActive(): int
    {
        return count($this->active());
    }

    public function countDiscarded(): int
    {
        return count($this->discarded());
    }

    public function countMerged(): int
    {
        return count($this->merged());
    }

    public function countOverridden(): int
    {
        return count($this->overridden());
    }

    public function highestScore(): ?Candidate
    {
        if (empty($this->candidates)) return null;
        $highest = null;
        foreach ($this->candidates as $c) {
            if ($highest === null || $c->matchScore + $c->inferenceScore > $highest->matchScore + $highest->inferenceScore) {
                $highest = $c;
            }
        }
        return $highest;
    }

    public function lowestScore(): ?Candidate
    {
        if (empty($this->candidates)) return null;
        $lowest = null;
        foreach ($this->candidates as $c) {
            if ($lowest === null || $c->matchScore + $c->inferenceScore < $lowest->matchScore + $lowest->inferenceScore) {
                $lowest = $c;
            }
        }
        return $lowest;
    }

    public function groupBySection(): array
    {
        $groups = [];
        foreach ($this->candidates as $c) {
            $groups[$c->rule->section][] = $c;
        }
        return $groups;
    }

    public function groupByVisibility(): array
    {
        $groups = [];
        foreach ($this->candidates as $c) {
            $groups[$c->rule->visibility][] = $c;
        }
        return $groups;
    }

    public function groupByFeature(): array
    {
        $groups = [];
        foreach ($this->candidates as $c) {
            $groups[$c->rule->feature][] = $c;
        }
        return $groups;
    }

    public function averageConfidence(): float
    {
        if (empty($this->candidates)) return 0.0;
        $total = 0;
        foreach ($this->candidates as $c) {
            $total += $c->confidence->final;
        }
        return $total / count($this->candidates);
    }

    public function all(): array
    {
        return $this->candidates;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->candidates);
    }

    public function count(): int
    {
        return count($this->candidates);
    }
}
