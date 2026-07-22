<?php

namespace AIAnalysisEngine\Inference\DTO;

enum Reason: string
{
    case MATCHED = 'MATCHED';
    case REJECTED = 'REJECTED';
    case OVERRIDDEN = 'OVERRIDDEN';
    case MERGED = 'MERGED';
    case ESCALATED = 'ESCALATED';
}
