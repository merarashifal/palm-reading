<?php

namespace AIAnalysisEngine\Inference\DTO;

class CandidateHistoryEntry
{
    public string $stage;
    public string $action;
    public string $timestamp;
    public ReasonCode $reason;

    public function __construct(string $stage, string $action, ReasonCode $reason)
    {
        $this->stage = $stage;
        $this->action = $action;
        $this->timestamp = date('c');
        $this->reason = $reason;
    }
}
