<?php

namespace AIAnalysisEngine\AI\DTO;

interface InputArtifact
{
    public function getType(): string;
    public function getPath(): string;
    public function getMimeType(): string;
}
