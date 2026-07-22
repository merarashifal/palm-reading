<?php

namespace AIAnalysisEngine\AI\DTO;

class StructuredProviderPayload
{
    public array $payload;
    public array $metadata;
    public ProviderIdentity $identity;

    public function __construct(array $payload, array $metadata, ProviderIdentity $identity)
    {
        $this->payload = $payload;
        $this->metadata = $metadata;
        $this->identity = $identity;
    }
}
