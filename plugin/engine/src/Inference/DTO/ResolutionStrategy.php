<?php

namespace AIAnalysisEngine\Inference\DTO;

enum ResolutionStrategy: string
{
    case OVERRIDE = 'OVERRIDE';
    case MERGE = 'MERGE';
    case ESCALATE = 'ESCALATE';
    case DISCARD = 'DISCARD';
}
