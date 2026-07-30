<?php

namespace AIAnalysisEngine\Observability\DTO;

class ProviderTrace extends StageTrace
{
    public function __construct(
        string $stage,
        float $start,
        float $end,
        float $duration,
        string $status,
        int $itemsIn,
        int $itemsOut,
        int $memoryBefore,
        int $memoryAfter,
        ?string $reason = null,
        public int $requestSizeBytes = 0,
        public int $responseSizeBytes = 0,
        public int $promptTokens = 0,
        public int $completionTokens = 0,
        public int $totalTokens = 0,
        public float $estimatedCost = 0.0,
        public float $queueTimeMs = 0.0,
        public float $networkLatencyMs = 0.0
    ) {
        parent::__construct(
            $stage, $start, $end, $duration, $status, $itemsIn, $itemsOut, $memoryBefore, $memoryAfter, $reason
        );
    }

    public function jsonSerialize(): array
    {
        $base = parent::jsonSerialize();
        return array_merge($base, [
            'requestSizeBytes' => $this->requestSizeBytes,
            'responseSizeBytes' => $this->responseSizeBytes,
            'promptTokens' => $this->promptTokens,
            'completionTokens' => $this->completionTokens,
            'totalTokens' => $this->totalTokens,
            'estimatedCost' => $this->estimatedCost,
            'queueTimeMs' => $this->queueTimeMs,
            'networkLatencyMs' => $this->networkLatencyMs
        ]);
    }
}
