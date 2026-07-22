<?php

namespace AIAnalysisEngine\Inference\DTO;

class PermissionContext
{
    public string $subscription;
    public string $plan;
    public array $capabilities = [];
    public array $scopes = [];
    public array $roles = [];

    public function __construct(
        string $subscription = 'free',
        string $plan = 'basic',
        array $capabilities = [],
        array $scopes = [],
        array $roles = []
    ) {
        $this->subscription = $subscription;
        $this->plan = $plan;
        $this->capabilities = $capabilities;
        $this->scopes = $scopes;
        $this->roles = $roles;
    }
}
