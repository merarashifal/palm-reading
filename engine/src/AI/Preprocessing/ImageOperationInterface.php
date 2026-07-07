<?php

namespace AIAnalysisEngine\AI\Preprocessing;

use AIAnalysisEngine\AI\DTO\ImageArtifact;

interface ImageOperationInterface
{
    public function apply(ImageArtifact $artifact): ImageArtifact;
}
