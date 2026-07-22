<?php

namespace AIAnalysisEngine\Inference\DTO;

enum CandidateStatus: string
{
    case NEW = 'NEW';
    case MATCHED = 'MATCHED';
    case SCORED = 'SCORED';
    case ACTIVE = 'ACTIVE';
    case MERGED = 'MERGED';
    case OVERRIDDEN = 'OVERRIDDEN';
    case DISCARDED = 'DISCARDED';
    case ESCALATED = 'ESCALATED';
}
