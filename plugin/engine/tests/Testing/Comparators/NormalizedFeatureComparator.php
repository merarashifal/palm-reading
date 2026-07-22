<?php

namespace AIAnalysisEngine\Testing\Comparators;

use AIAnalysisEngine\Inference\DTO\FeatureCollection;

class NormalizedFeatureComparator extends AbstractComparator
{
    public function __construct(
        private float $confidenceTolerance = 0.05,
        private float $coordinateTolerance = 0.02
    ) {
    }

    public function compare(mixed $expected, mixed $actual): bool
    {
        if (!$expected instanceof FeatureCollection || !$actual instanceof FeatureCollection) {
            throw new \InvalidArgumentException("Expected FeatureCollection instances.");
        }

        $expectedFeatures = $expected->getAll();
        $actualFeatures = $actual->getAll();

        if (count($expectedFeatures) !== count($actualFeatures)) {
            throw new \RuntimeException(sprintf(
                "Feature count mismatch. Expected: %d, Actual: %d",
                count($expectedFeatures), count($actualFeatures)
            ));
        }

        foreach ($expectedFeatures as $index => $expectedFeature) {
            $actualFeature = $actualFeatures[$index] ?? null;

            if (!$actualFeature) {
                throw new \RuntimeException("Missing actual feature at index {$index}");
            }

            // Semantic Equivalence
            if ($expectedFeature->name !== $actualFeature->name) {
                throw new \RuntimeException("Feature name mismatch. Expected: {$expectedFeature->name}, Actual: {$actualFeature->name}");
            }
            if ($expectedFeature->value !== $actualFeature->value) {
                throw new \RuntimeException("Feature value mismatch. Expected: {$expectedFeature->value}, Actual: {$actualFeature->value}");
            }

            // Tolerance checks
            $this->assertFloatTolerance(
                $expectedFeature->confidence->score, 
                $actualFeature->confidence->score, 
                $this->confidenceTolerance, 
                "confidence"
            );
        }

        return true;
    }
}
