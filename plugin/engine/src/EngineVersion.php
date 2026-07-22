<?php

namespace AIAnalysisEngine;

class EngineVersion {
    public const VERSION = '1.0.0';
    
    public static function get(): string {
        return self::VERSION;
    }
}
