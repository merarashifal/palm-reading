<?php

namespace AIAnalysisEngine\AI\DTO;

class AnalysisDefinition
{
    public readonly string $uuid;
    public readonly string $name;
    public readonly string $version;
    
    // Identifiers for AssetRegistry resolution
    public readonly string $promptAssetId;
    public readonly string $schemaAssetId;
    public readonly string $calibrationAssetId;
    
    public readonly string $preprocessingProfile;
    public readonly string $providerProfile;

    public function __construct(
        string $uuid,
        string $name,
        string $version,
        string $promptAssetId,
        string $schemaAssetId,
        string $calibrationAssetId,
        string $preprocessingProfile,
        string $providerProfile
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->version = $version;
        $this->promptAssetId = $promptAssetId;
        $this->schemaAssetId = $schemaAssetId;
        $this->calibrationAssetId = $calibrationAssetId;
        $this->preprocessingProfile = $preprocessingProfile;
        $this->providerProfile = $providerProfile;
    }
}
