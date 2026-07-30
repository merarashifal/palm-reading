<?php

namespace AIAnalysisEngine\AI\Providers\Gemini;

use RuntimeException;

class GeminiResponse
{
    private string $rawJson;
    private array $decodedJson;
    private array $extractedPayload;

    public function __construct(string $rawJson)
    {
        $this->rawJson = $rawJson;
        $this->parse();
    }

    private function parse(): void
    {
        $this->decodedJson = json_decode($this->rawJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Invalid JSON received from Gemini: " . json_last_error_msg());
        }

        if (!isset($this->decodedJson['candidates'][0]['content']['parts'][0]['text'])) {
            throw new RuntimeException("Unexpected Gemini response structure.");
        }

        $text = $this->decodedJson['candidates'][0]['content']['parts'][0]['text'];
        
        // Strip Markdown
        $text = preg_replace('/```json\s*/', '', $text);
        $text = preg_replace('/```\s*/', '', $text);
        
        $this->extractedPayload = json_decode($text, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Gemini output payload is not valid JSON: " . json_last_error_msg() . "\nPayload:\n" . $text);
        }
    }

    public function getExtractedPayload(): array
    {
        return $this->extractedPayload;
    }

    public function getRawJson(): string
    {
        return $this->rawJson;
    }
}
