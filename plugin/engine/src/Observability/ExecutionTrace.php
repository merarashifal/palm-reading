<?php

namespace AIAnalysisEngine\Observability;

use AIAnalysisEngine\Observability\DTO\StageTrace;
use AIAnalysisEngine\Observability\DTO\PerformanceProfile;

class ExecutionTrace implements \JsonSerializable
{
    public string $sessionId;
    public string $requestId;
    public string $correlationId;

    /** @var StageTrace[] */
    public array $stages = [];

    public PerformanceProfile $profile;

    public function __construct(
        string $sessionId,
        string $requestId,
        string $correlationId
    ) {
        $this->sessionId = $sessionId;
        $this->requestId = $requestId;
        $this->correlationId = $correlationId;
        $this->profile = new PerformanceProfile();
    }

    public function addStage(StageTrace $trace): void
    {
        $this->stages[] = $trace;
    }

    public function jsonSerialize(): array
    {
        return [
            'sessionId' => $this->sessionId,
            'requestId' => $this->requestId,
            'correlationId' => $this->correlationId,
            'stages' => array_map(fn($stage) => $stage->jsonSerialize(), $this->stages),
            'profile' => $this->profile->jsonSerialize()
        ];
    }
}
