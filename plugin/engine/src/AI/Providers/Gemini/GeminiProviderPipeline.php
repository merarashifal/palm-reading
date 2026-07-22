<?php

namespace AIAnalysisEngine\AI\Providers\Gemini;

use AIAnalysisEngine\AI\DTO\NormalizedFeatureCollection;
use AIAnalysisEngine\AI\DTO\NormalizedFeature;
use AIAnalysisEngine\AI\Providers\DTO\ExtractedFeatureCollection;

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

    private function normalize(ExtractedFeatureCollection $extracted): NormalizedFeatureCollection
    {
        $features = [];
        $provider = "gemini";
        $providerVersion = $extracted->getSchemaVersion();

        // Process Generic Features
        foreach ($extracted->getFeatures() as $feat) {
            // In the generic model, the provider type/category maps cleanly to the NormalizedFeature signature
            $features[] = new NormalizedFeature(
                $feat->getId(),
                'palm',
                $feat->getType(),
                $feat->getCategory(), // We map category to 'value' temporarily for inference consumption
                $feat->getVisualConfidence(),
                1,
                'image',
                $provider,
                $providerVersion,
                null, // Geometry mapping would go here in a robust BoundingRegion system
                [
                    'status' => $feat->getStatus(),
                    'evidence' => $feat->getEvidence(),
                    'geometry' => $feat->getGeometry(),
                    'bbox' => $feat->getBbox(),
                    'geometry_confidence' => $feat->getGeometryConfidence(),
                    'feature_revision' => $feat->getRevision(),
                    'attributes' => $feat->getAttributes()
                ]
            );
        }

        $hand = $extracted->getHand();
        if ($hand) {
            $features[] = new NormalizedFeature(
                uniqid('hand_'),
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
                uniqid('qual_'),
                'palm',
                'image_quality',
                $quality['lighting'] ?? 'unknown',
                1.0,
                1,
                'image',
                $provider,
                $providerVersion,
                null,
                [
                    'score' => $quality['score'] ?? 0, 
                    'blur' => $quality['blur'] ?? 0.0,
                    'shadow_score' => $quality['shadow_score'] ?? 0,
                    'contrast_score' => $quality['contrast_score'] ?? 0,
                    'rotation_angle' => $quality['rotation_angle'] ?? 0,
                    'crop_quality' => $quality['crop_quality'] ?? 'unknown',
                    'recommendations' => $quality['recommendations'] ?? []
                ]
            );
        }

        $collection = new NormalizedFeatureCollection();
        foreach ($features as $f) {
            $collection->add($f);
        }
        return $collection;
    }
}
