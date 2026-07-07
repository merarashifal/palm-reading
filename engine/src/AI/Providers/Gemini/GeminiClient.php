<?php

namespace AIAnalysisEngine\AI\Providers\Gemini;

use RuntimeException;

class GeminiClient
{
    private string $apiKey;
    private string $model;
    private string $endpoint;
    private string $storageDir;

    public function __construct(string $apiKey, string $model, string $endpoint, string $storageDir)
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->endpoint = $endpoint;
        $this->storageDir = $storageDir;
        
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }
    }

    public function analyzeImage(string $imagePath, string $promptPath): string
    {
        if (!file_exists($imagePath)) {
            throw new RuntimeException("Image file not found: " . $imagePath);
        }
        
        if (!file_exists($promptPath)) {
            throw new RuntimeException("Prompt file not found: " . $promptPath);
        }

        $prompt = file_get_contents($promptPath);
        $imageData = file_get_contents($imagePath);
        $base64Image = base64_encode($imageData);
        $mimeType = mime_content_type($imagePath);
        if (!$mimeType) {
            $mimeType = 'image/jpeg';
        }
        $imageHash = hash('sha256', $imageData);

        // Date-based subfolder
        $dateFolder = date('Y-m-d');
        $runId = uniqid('gemini_');
        $saveDir = $this->storageDir . '/' . $dateFolder . '/' . $runId;
        
        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0755, true);
        }

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
        
        // Save prompt and request
        file_put_contents($saveDir . '/prompt.txt', "Image Hash: $imageHash\n\n" . $prompt);
        // Don't save the full base64 in request to save space, just structure
        $debugPayload = $payload;
        $debugPayload['contents'][0]['parts'][1]['inline_data']['data'] = '<BASE64_TRUNCATED>';
        file_put_contents($saveDir . '/request.json', json_encode($debugPayload, JSON_PRETTY_PRINT));

        $url = sprintf("%s?key=%s", $this->endpoint, $this->apiKey);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestJson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        
        $startTime = microtime(true);
        $response = curl_exec($ch);
        $latency = microtime(true) - $startTime;
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) {
            $errorDir = $this->storageDir . '/failed/' . $dateFolder;
            if (!is_dir($errorDir)) {
                mkdir($errorDir, 0755, true);
            }
            file_put_contents($errorDir . '/' . $runId . '_response.txt', "HTTP $httpCode\n$response");
            throw new RuntimeException("Gemini API request failed. HTTP Code: " . $httpCode);
        }

        // Save raw response
        file_put_contents($saveDir . '/response.json', $response);
        
        // Check if JSON is valid
        json_decode($response);
        $jsonValid = (json_last_error() === JSON_ERROR_NONE);

        file_put_contents($saveDir . '/metrics.json', json_encode([
            'model' => $this->model,
            'prompt_path' => $promptPath,
            'latency_seconds' => $latency,
            'image_hash' => $imageHash,
            'json_valid' => $jsonValid
        ], JSON_PRETTY_PRINT));

        if (!$jsonValid) {
            $errorDir = $this->storageDir . '/failed/' . $dateFolder;
            if (!is_dir($errorDir)) {
                mkdir($errorDir, 0755, true);
            }
            file_put_contents($errorDir . '/' . $runId . '_invalid_json.txt', $response);
            throw new RuntimeException("Gemini returned invalid JSON");
        }

        return $response;
    }
}
