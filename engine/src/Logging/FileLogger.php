<?php

namespace AIAnalysisEngine\Logging;

use AIAnalysisEngine\Contracts\LoggerInterface;

class FileLogger implements LoggerInterface {
    private string $logFile;

    public function __construct(string $logFile) {
        $this->logFile = $logFile;
        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }
    }

    private function log(string $level, string $message, array $context = []): void {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logEntry = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }

    public function info(string $message, array $context = []): void {
        $this->log('INFO', $message, $context);
    }

    public function warning(string $message, array $context = []): void {
        $this->log('WARNING', $message, $context);
    }

    public function error(string $message, array $context = []): void {
        $this->log('ERROR', $message, $context);
    }
}
