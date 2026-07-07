<?php

namespace AIAnalysisEngine\Inference\DTO;

enum MatchType: string
{
    case EXACT = 'EXACT';
    case PREFIX = 'PREFIX';
    case PARTIAL = 'PARTIAL';
    case FUZZY = 'FUZZY';
    case FALLBACK = 'FALLBACK';
}
