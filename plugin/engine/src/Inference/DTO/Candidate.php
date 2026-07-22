<?php

namespace AIAnalysisEngine\Inference\DTO;

use AIAnalysisEngine\Knowledge\Compiled\CompiledRule;

class Candidate
{
    public string $id;
    public CompiledRule $rule;
    public EvidenceCollection $evidence;
    public float $matchScore;
    public float $inferenceScore;
    public float $finalScore;
    public ConfidenceResult $confidence;
    public CandidateStatus $status;
    /** @var CandidateHistoryEntry[] */
    public array $history = [];

    public function __construct(
        string $id,
        CompiledRule $rule,
        EvidenceCollection $evidence
    ) {
        $this->id = $id;
        $this->rule = $rule;
        $this->evidence = $evidence;
        $this->matchScore = 0.0;
        $this->inferenceScore = 0.0;
        $this->finalScore = 0.0;
        $this->confidence = new ConfidenceResult();
        $this->status = CandidateStatus::NEW;
    }

    public function withMatchScore(float $score, string $stage, ReasonCode $reason): self
    {
        $clone = clone $this;
        $clone->matchScore = $score;
        $clone->status = CandidateStatus::MATCHED;
        $clone->history[] = new CandidateHistoryEntry($stage, 'Matched', $reason);
        return $clone;
    }

    public function withInferenceScore(float $score, string $stage, ReasonCode $reason): self
    {
        $clone = clone $this;
        $clone->inferenceScore = $score;
        $clone->status = CandidateStatus::SCORED;
        $clone->history[] = new CandidateHistoryEntry($stage, 'Scored', $reason);
        return $clone;
    }

    public function withStatus(CandidateStatus $status, string $stage, ReasonCode $reason): self
    {
        $clone = clone $this;
        $clone->status = $status;
        $clone->history[] = new CandidateHistoryEntry($stage, "Status Changed to {$status->name}", $reason);
        return $clone;
    }

    public function withConfidence(ConfidenceResult $confidence, string $stage, ReasonCode $reason): self
    {
        $clone = clone $this;
        $clone->confidence = $confidence;
        $clone->history[] = new CandidateHistoryEntry($stage, 'Confidence Resolved', $reason);
        return $clone;
    }
}
