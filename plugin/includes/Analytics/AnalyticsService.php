<?php

namespace AIAnalysisEngine\Analytics;

class AnalyticsService
{
    private static string $storagePath;

    public static function init(string $storageBasePath)
    {
        self::$storagePath = $storageBasePath . '/analytics';
        if (!is_dir(self::$storagePath)) {
            mkdir(self::$storagePath, 0777, true);
        }
    }

    public static function getVisitorId(): string
    {
        if (isset($_COOKIE['ppb_visitor_id'])) {
            return sanitize_text_field($_COOKIE['ppb_visitor_id']);
        }
        $vid = 'vis_' . strtoupper(substr(md5(uniqid()), 0, 8));
        setcookie('ppb_visitor_id', $vid, time() + (86400 * 365), '/');
        return $vid;
    }

    public static function log(string $event, string $reportId, string $visitorId, array $payload = []): void
    {
        if (empty(self::$storagePath)) return;
        
        $logFile = self::$storagePath . '/events_' . date('Y-m-d') . '.log';
        $entry = [
            'timestamp' => date('c'),
            'event' => $event,
            'report_id' => $reportId,
            'visitor_id' => $visitorId,
            'payload' => $payload
        ];
        
        file_put_contents($logFile, json_encode($entry) . PHP_EOL, FILE_APPEND);
    }
}
