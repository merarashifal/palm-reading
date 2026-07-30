<?php

namespace AIAnalysisEngine\Knowledge\Compiled;

class CompiledRuleCollection implements \IteratorAggregate, \Countable
{
    private array $rules = [];
    public RuleIndex $index;

    public function __construct()
    {
        $this->index = new RuleIndex();
    }

    public function add(CompiledRule $rule): void
    {
        $this->rules[] = $rule;
        $this->index->indexRule($rule);
    }

    public function get(string $uid): ?CompiledRule
    {
        return $this->index->getByUid($uid);
    }

    public function findByFeature(string $feature): iterable
    {
        return $this->index->getByFeature($feature);
    }

    public function findByVisibility(string $visibility): iterable
    {
        return $this->index->getByVisibility($visibility);
    }

    public function findBySection(string $section): iterable
    {
        return $this->index->getBySection($section);
    }

    public function findByUid(string $uid): ?CompiledRule
    {
        return $this->index->getByUid($uid);
    }

    public function all(): array
    {
        return $this->rules;
    }

    public function count(): int
    {
        return count($this->rules);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->rules);
    }
}
