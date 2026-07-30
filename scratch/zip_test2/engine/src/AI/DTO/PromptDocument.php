<?php

namespace AIAnalysisEngine\AI\DTO;

class PromptDocument
{
    public string $systemPrompt;
    public string $userPrompt;
    public array $schema;
    public array $examples = [];
    public array $attachments = [];
    public array $metadata = [];

    public function __construct(string $systemPrompt, string $userPrompt, array $schema)
    {
        $this->systemPrompt = $systemPrompt;
        $this->userPrompt = $userPrompt;
        $this->schema = $schema;
    }
}
