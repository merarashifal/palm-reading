<?php

namespace AIAnalysisEngine\AI\Providers\Gemini;

use AIAnalysisEngine\AI\Providers\DTO\ExtractedFeatureCollection;

class GeminiFeatureExtractor
{
    public function extract(GeminiResponse $response): ExtractedFeatureCollection
    {
        $payload = $response->getExtractedPayload();
        
        // This acts as the geometry mapper boundary.
        // If we needed to translate Gemini-specific geometry concepts to our standard extraction format, 
        // it would happen here. For now, since the prompt is designed exactly for our structure, 
        // we just wrap it in the DTO.

        return new ExtractedFeatureCollection($payload);
    }
}
