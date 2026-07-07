<?php

namespace AIAnalysisEngine\Inference\DTO;

class InferenceStatistics
{
    public int $featuresReceived = 0;
    public int $evidenceCreated = 0;
    public int $rulesMatched = 0;
    public int $candidatesCreated = 0;
    public int $candidatesActive = 0;
    public int $candidatesDiscarded = 0;
    public int $candidatesMerged = 0;
    public int $candidatesOverridden = 0;
    public float $averageMatchScore = 0.0;
    public float $averageInferenceScore = 0.0;
    public float $averageConfidence = 0.0;
    public float $totalExecutionTime = 0.0;
}
