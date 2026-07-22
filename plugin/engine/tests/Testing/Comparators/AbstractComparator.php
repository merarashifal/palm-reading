<?php

namespace AIAnalysisEngine\Testing\Comparators;

abstract class AbstractComparator
{
    /**
     * Compares two objects and throws an exception if they do not match.
     */
    abstract public function compare(mixed $expected, mixed $actual): bool;

    protected function assertFloatTolerance(float $expected, float $actual, float $tolerance, string $field): void
    {
        if (abs($expected - $actual) > $tolerance) {
            throw new \RuntimeException(sprintf(
                "Field '%s' differs by more than tolerance %f. Expected: %f, Actual: %f",
                $field, $tolerance, $expected, $actual
            ));
        }
    }
}
