<?php

namespace AIAnalysisEngine\Logging;

use AIAnalysisEngine\Contracts\LoggerInterface;

class NullLogger implements LoggerInterface {
    public function info(string $message, array $context = []): void {}
    public function warning(string $message, array $context = []): void {}
    public function error(string $message, array $context = []): void {}
}
