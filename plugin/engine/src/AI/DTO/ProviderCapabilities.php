<?php

namespace AIAnalysisEngine\AI\DTO;

class ProviderCapabilities
{
    public bool $supportsImages = false;
    public bool $supportsVideo = false;
    public bool $supportsAudio = false;
    public bool $supportsPDF = false;
    public bool $supportsStreaming = false;
    public bool $supportsBatch = false;
    public bool $supportsFunctionCalling = false;
    public bool $supportsJSONMode = false;
    public bool $supportsThinking = false;
    public bool $supportsStructuredOutput = false;

    public int $maxTokens = 0;
    public int $maxImages = 0;
}
