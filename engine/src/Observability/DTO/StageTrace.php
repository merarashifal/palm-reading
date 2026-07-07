<?php

namespace AIAnalysisEngine\Observability\DTO;

class StageTrace implements \JsonSerializable
{
    public function __construct(
        public string $stage,
        public float $start,
        public float $end,
        public float $duration,
        public string $status,
        public int $itemsIn,
        public int $itemsOut,
        public int $memoryBefore,
        public int $memoryAfter,
        public ?string $reason = null
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'stage' => $this->stage,
            'start' => $this->start,
            'end' => $this->end,
            'duration' => $this->duration,
            'status' => $this->status,
            'itemsIn' => $this->itemsIn,
            'itemsOut' => $this->itemsOut,
            'memoryBefore' => $this->memoryBefore,
            'memoryAfter' => $this->memoryAfter,
            'reason' => $this->reason
        ];
    }
}
