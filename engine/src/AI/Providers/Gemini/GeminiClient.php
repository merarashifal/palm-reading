<?php

namespace AIAnalysisEngine\AI\Providers\Gemini;

use AIAnalysisEngine\Exception\EngineException;
use AIAnalysisEngine\AI\Providers\DTO\GeminiExecution;

class GeminiClient
{
    private string $apiKey;
    private string $model;
    private string $endpoint;
    private string $runDir;

    public function __construct(string $apiKey, string $model, string $endpoint, string $runDir)
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->endpoint = $endpoint;
        $this->runDir = $runDir;
        
        if (!is_dir($this->runDir)) {
            mkdir($this->runDir, 0755, true);
        }
    }

    public function analyzeImage(string $imagePath, string $promptPath): string
    {
        if (!file_exists($imagePath)) {
            throw new EngineException('SYS_002', "Image file not found: " . $imagePath);
        }
        
        if (!file_exists($promptPath)) {
            throw new EngineException('SYS_003', "Prompt file not found: " . $promptPath);
        }

        $prompt = file_get_contents($promptPath);
        $imageData = file_get_contents($imagePath);
        $base64Image = base64_encode($imageData);
        $mimeType = mime_content_type($imagePath);
        if (!$mimeType) {
            $mimeType = 'image/jpeg';
        }
        $imageHash = hash('sha256', $imageData);

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $base64Image
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
            ]
        ];

        $requestJson = json_encode($payload, JSON_PRETTY_PRINT);
        
        // Save prompt and request to Run ID dir
        file_put_contents($this->runDir . '/prompt.txt', "Image Hash: $imageHash\n\n" . $prompt);
        
        $debugPayload = $payload;
        $debugPayload['contents'][0]['parts'][1]['inline_data']['data'] = '<BASE64_TRUNCATED>';
        file_put_contents($this->runDir . '/request.json', json_encode($debugPayload, JSON_PRETTY_PRINT));

        $url = sprintf("%s?key=%s", $this->endpoint, $this->apiKey);

        $maxRetries = 3;
        $attempt = 0;
        $response = false;
        $httpCode = 0;
        
        $globalStartTime = microtime(true);
        $latencyMs = 0;

        while ($attempt < $maxRetries) {
            $attempt++;
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestJson);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 45);
            
            $startTime = microtime(true);
            $response = curl_exec($ch);
            $latencyMs = (microtime(true) - $startTime) * 1000;
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response === false || in_array($httpCode, [429, 500, 503])) {
                if ($attempt < $maxRetries) {
                    sleep((int)pow(2, $attempt)); 
                    continue;
                }
            }
            break;
        }

        $globalFinishedAt = microtime(true);
        $totalDurationMs = ($globalFinishedAt - $globalStartTime) * 1000;

        if ($response === false) {
            throw new EngineException('AI_001', "Gemini API timeout or connection failed.");
        }
        
        if ($httpCode === 429) {
            throw new EngineException('AI_002', "Gemini API quota exceeded.");
        }
        
        if ($httpCode >= 400) {
            file_put_contents($this->runDir . '/response_error.txt', "HTTP $httpCode\n$response");
            throw new EngineException('AI_003', "Gemini API request failed with HTTP $httpCode");
        }

        // Save raw response
        file_put_contents($this->runDir . '/response.json', $response);
        
        // Check if JSON is valid
        $decoded = json_decode($response, true);
        $jsonValid = (json_last_error() === JSON_ERROR_NONE);

        // Extract metrics
        $promptTokens = $decoded['usageMetadata']['promptTokenCount'] ?? 0;
        $completionTokens = $decoded['usageMetadata']['candidatesTokenCount'] ?? 0;
        
        $costUsd = ($promptTokens / 1000000) * 0.075 + ($completionTokens / 1000000) * 0.30;
        $costInr = $costUsd * 83.5;

        // Build execution object
        $execution = new GeminiExecution(
            $globalStartTime,
            $globalFinishedAt,
            $totalDurationMs,
            $httpCode,
            $promptTokens,
            $completionTokens,
            round($costInr, 4),
            $attempt - 1
        );

        // Write execution metrics to run dir (EngineFacade will append its own later)
        file_put_contents($this->runDir . '/gemini_metrics.json', json_encode($execution->toArray(), JSON_PRETTY_PRINT));

        if (!$jsonValid) {
            file_put_contents($this->runDir . '/response_invalid_json.txt', $response);
            throw new EngineException('AI_004', "Gemini returned invalid JSON");
        }

        return $response;
    }
}
