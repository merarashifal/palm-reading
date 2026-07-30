<?php

namespace AIAnalysisEngine\Inference\DTO;

class MatchedRuleCollection implements \IteratorAggregate, \Countable
{
    /** @var MatchedRule[] */
    private array $matches = [];

    public function add(MatchedRule $match): void
    {
        // Simple append since multiple evidence could match the same rule in different ways
        $this->matches[] = $match;
    }

    public function exactMatches(): array
    {
        return array_filter($this->matches, fn(MatchedRule $m) => $m->exactMatch);
    }

    public function fallbackMatches(): array
    {
        return array_filter($this->matches, fn(MatchedRule $m) => $m->lookupStrategy === MatchedRule::STRATEGY_FALLBACK);
    }

    public function groupBySection(): array
    {
        $groups = [];
        foreach ($this->matches as $m) {
            $groups[$m->rule->section][] = $m;
        }
        return $groups;
    }

    public function groupByVisibility(): array
    {
        $groups = [];
        foreach ($this->matches as $m) {
            $groups[$m->rule->visibility][] = $m;
        }
        return $groups;
    }

    public function groupByFeature(): array
    {
        $groups = [];
        foreach ($this->matches as $m) {
            foreach ($m->matchedFeatures as $f) {
                $groups[$f][] = $m;
            }
        }
        return $groups;
    }

    public function all(): array
    {
        return $this->matches;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->matches);
    }

    public function count(): int
    {
        return count($this->matches);
    }
}
