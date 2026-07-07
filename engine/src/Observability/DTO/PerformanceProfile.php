<?php

namespace AIAnalysisEngine\Observability\DTO;

class PerformanceProfile implements \JsonSerializable
{
    // Runtime
    public float $wallClockMs = 0.0;
    public float $cpuTimeMs = 0.0;
    public int $memoryBytes = 0;
    public int $peakMemoryBytes = 0;

    // Workload
    public int $featuresProcessed = 0;
    public int $rulesMatched = 0;
    public int $candidatesProduced = 0;
    public int $relationshipsExpanded = 0;
    public int $sectionsProduced = 0;

    public function jsonSerialize(): array
    {
        return [
            'runtime' => [
                'wallClockMs' => $this->wallClockMs,
                'cpuTimeMs' => $this->cpuTimeMs,
                'memoryBytes' => $this->memoryBytes,
                'peakMemoryBytes' => $this->peakMemoryBytes
            ],
            'workload' => [
                'featuresProcessed' => $this->featuresProcessed,
                'rulesMatched' => $this->rulesMatched,
                'candidatesProduced' => $this->candidatesProduced,
                'relationshipsExpanded' => $this->relationshipsExpanded,
                'sectionsProduced' => $this->sectionsProduced
            ]
        ];
    }
}
