<?php

namespace AIAnalysisEngine\Replay;

use AIAnalysisEngine\Observability\ExecutionTrace;
use AIAnalysisEngine\Inference\DTO\InferenceResult;
use AIAnalysisEngine\Inference\DTO\FeatureCollection; // Using FeatureCollection assuming NormalizedFeatureCollection maps to it.

class ReplayPackage implements \JsonSerializable
{
    public function __construct(
        public readonly string $replayVersion,
        public readonly string $engineVersion,
        public readonly string $knowledgePackVersion,
        public readonly string $analysisDefinitionVersion,
        public readonly string $providerVersion,
        public readonly string $schemaVersion,
        public readonly array $analysisDefinition,
        public readonly array $promptDocument,
        public readonly array $requestPayload,
        public readonly array $responsePayload,
        public readonly FeatureCollection $normalizedFeatures,
        public readonly InferenceResult $inferenceResult,
        public readonly ExecutionTrace $executionTrace
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'versions' => [
                'replayVersion' => $this->replayVersion,
                'engineVersion' => $this->engineVersion,
                'knowledgePackVersion' => $this->knowledgePackVersion,
                'analysisDefinitionVersion' => $this->analysisDefinitionVersion,
                'providerVersion' => $this->providerVersion,
                'schemaVersion' => $this->schemaVersion,
            ],
            'payloads' => [
                'analysisDefinition' => $this->analysisDefinition,
                'promptDocument' => $this->promptDocument,
                'requestPayload' => $this->requestPayload,
                'responsePayload' => $this->responsePayload,
            ],
            'outputs' => [
                'normalizedFeatures' => $this->normalizedFeatures,
                'inferenceResult' => $this->inferenceResult,
                'executionTrace' => $this->executionTrace,
            ]
        ];
    }
}
