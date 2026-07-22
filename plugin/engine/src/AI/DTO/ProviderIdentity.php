<?php

namespace AIAnalysisEngine\AI\DTO;

class ProviderIdentity
{
    public string $provider;
    public string $model;
    public string $revision;
    public string $apiVersion;
    public string $region;
    public string $endpoint;
    /** @var string[] */
    public array $capabilities = [];

    public function __construct(
        string $provider, 
        string $model, 
        string $revision,
        string $apiVersion = 'v1',
        string $region = 'global',
        string $endpoint = '',
        array $capabilities = []
    ) {
        $this->provider = $provider;
        $this->model = $model;
        $this->revision = $revision;
        $this->apiVersion = $apiVersion;
        $this->region = $region;
        $this->endpoint = $endpoint;
        $this->capabilities = $capabilities;
    }
}
