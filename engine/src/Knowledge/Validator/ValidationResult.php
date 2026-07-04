<?php

namespace AIAnalysisEngine\Knowledge\Validator;

class ValidationResult implements \JsonSerializable {
    public const STATUS_PASS = 'PASS';
    public const STATUS_WARNING = 'WARNING';
    public const STATUS_FAIL = 'FAIL';
    public const STATUS_SKIPPED = 'SKIPPED';

    private string $validator;
    private string $status;
    private array $scores;
    private array $errors;
    private array $warnings;
    private array $statistics;
    private float $executionTime;
    private int $memoryUsage;
    private string $timestamp;

    public function __construct(
        string $validator,
        string $status,
        array $scores = [],
        array $errors = [],
        array $warnings = [],
        array $statistics = [],
        float $executionTime = 0.0,
        int $memoryUsage = 0
    ) {
        $this->validator = $validator;
        $this->status = $status;
        $this->scores = $scores;
        $this->errors = $errors;
        $this->warnings = $warnings;
        $this->statistics = $statistics;
        $this->executionTime = $executionTime;
        $this->memoryUsage = $memoryUsage;
        $this->timestamp = gmdate('Y-m-d\TH:i:s\Z');
    }

    public function getValidator(): string { return $this->validator; }
    public function getStatus(): string { return $this->status; }
    public function getScores(): array { return $this->scores; }
    public function getErrors(): array { return $this->errors; }
    public function getWarnings(): array { return $this->warnings; }
    public function getStatistics(): array { return $this->statistics; }
    public function getExecutionTime(): float { return $this->executionTime; }
    public function getMemoryUsage(): int { return $this->memoryUsage; }
    public function getTimestamp(): string { return $this->timestamp; }

    public function jsonSerialize(): array {
        return [
            'validator' => $this->validator,
            'status' => $this->status,
            'scores' => $this->scores,
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'statistics' => $this->statistics,
            'executionTime' => $this->executionTime,
            'memoryUsage' => $this->memoryUsage,
            'timestamp' => $this->timestamp,
        ];
    }
}
