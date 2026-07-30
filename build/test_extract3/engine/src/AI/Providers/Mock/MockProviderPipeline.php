<?php

namespace AIAnalysisEngine\AI\Providers\Mock;

use AIAnalysisEngine\AI\Contracts\AIAdapterInterface;
use AIAnalysisEngine\AI\DTO\AnalysisSession;
use AIAnalysisEngine\AI\DTO\ProviderIdentity;
use AIAnalysisEngine\AI\DTO\ProviderResponse;
use AIAnalysisEngine\AI\DTO\NormalizedFeatureCollection;
use AIAnalysisEngine\AI\DTO\NormalizedFeature;
use AIAnalysisEngine\AI\DTO\BoundingRegion;

class MockProviderPipeline implements AIAdapterInterface
{
    public function execute(AnalysisSession $session): AnalysisSession
    {
        $startTime = microtime(true);

        // 1. Preprocessing (Mock)
        $session->recordTimeline('Preprocess', 35.0);

        // 2. Request Builder (Mock)
        $session->recordTimeline('Request', 4.0);

        // 3. Transport (Mock)
        $session->identity = new ProviderIdentity('MockProvider', 'v1.0', '1.0');
        $session->response = new ProviderResponse(200, [], json_encode(['raw' => 'success']));
        $session->recordTimeline('HTTP', 120.0);

        // 4. Parser (Mock)
        $session->response->parsedResponse = ['raw' => 'success'];
        $session->recordTimeline('Parse', 2.0);

        // 5. Calibrator & Normalizer (Mock)
        $features = new NormalizedFeatureCollection();
        $features->add(new NormalizedFeature(
            'feat_mock_1',
            'palm',
            'life_line',
            'broken',
            0.95,
            100,
            'mock',
            'mock',
            '1.0',
            new BoundingRegion('rectangle', [['x' => 0.1, 'y' => 0.1]])
        ));
        
        $session->features = $features;
        $session->recordTimeline('Normalize', 1.0);

        $session->duration = (microtime(true) - $startTime) * 1000;
        $session->success = true;

        return $session;
    }
}
