<?php

namespace AIAnalysisEngine\AI\Providers\Gemini;

use AIAnalysisEngine\AI\DTO\NormalizedFeatureCollection;
use AIAnalysisEngine\AI\DTO\NormalizedFeature;

class GeminiProviderPipeline
{
    private GeminiClient $client;
    private GeminiFeatureExtractor $extractor;

    public function __construct(GeminiClient $client)
    {
        $this->client = $client;
        $this->extractor = new GeminiFeatureExtractor();
    }

    public function run(string $imagePath, string $promptPath): NormalizedFeatureCollection
    {
        // 1. HTTP Request
        $rawJson = $this->client->analyzeImage($imagePath, $promptPath);

        // 2. Wrap Response
        $response = new GeminiResponse($rawJson);

        // 3. Extract Provider Features
        $extracted = $this->extractor->extract($response);

        // 4. Normalize to Engine Standards
        return $this->normalize($extracted);
    }

    private function normalize($extracted): NormalizedFeatureCollection
    {
        $features = [];
        $provider = "gemini";
        $providerVersion = "2.5-flash";

        // Lines
        foreach ($extracted->getLines() as $line) {
            // E.g., if it's a major_line, we just normalize it as a generic geometry line
            $features[] = new NormalizedFeature(
                $line['id'] ?? uniqid(),
                'palm',
                $line['type'] ?? 'unknown_line',
                $line['continuity'] ?? 'solid',
                $line['confidence'] ?? 0.5,
                1,
                'image',
                $provider,
                $providerVersion,
                null,
                ['depth' => $line['depth'] ?? 'faint', 'length' => $line['length'] ?? 0.0]
            );
        }

        // Mounts
        foreach ($extracted->getMounts() as $mount) {
            $features[] = new NormalizedFeature(
                $mount['id'] ?? uniqid(),
                'palm',
                'mount',
                $mount['prominence'] ?? 'normal',
                $mount['confidence'] ?? 0.5,
                1,
                'image',
                $provider,
                $providerVersion
            );
        }

        // Signs
        foreach ($extracted->getSigns() as $sign) {
            $features[] = new NormalizedFeature(
                $sign['id'] ?? uniqid(),
                'palm',
                'sign',
                $sign['type'] ?? 'unknown',
                $sign['confidence'] ?? 0.5,
                1,
                'image',
                $provider,
                $providerVersion
            );
        }

        $hand = $extracted->getHand();
        if ($hand) {
            $features[] = new NormalizedFeature(
                uniqid(),
                'palm',
                'hand_type',
                $hand,
                1.0,
                1,
                'image',
                $provider,
                $providerVersion
            );
        }
        
        $quality = $extracted->getImageQuality();
        if ($quality) {
            $features[] = new NormalizedFeature(
                uniqid(),
                'palm',
                'image_quality',
                $quality['lighting'] ?? 'unknown',
                1.0,
                1,
                'image',
                $provider,
                $providerVersion,
                null,
                ['score' => $quality['score'] ?? 0, 'blur' => $quality['blur'] ?? 0.0]
            );
        }

        return new NormalizedFeatureCollection($features);
    }
}
