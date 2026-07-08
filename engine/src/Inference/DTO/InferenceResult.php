<?php

namespace AIAnalysisEngine\Inference\DTO;

class InferenceResult
{
    /** @var Insight[] */
    public array $insights = [];
    
    /** @var Recommendation[] */
    public array $recommendations = [];
    
    public array $scores = [];
    public array $statistics = [];
    public array $metadata = [];
}
