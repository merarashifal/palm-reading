<?php

namespace AIAnalysisEngine\Storage;

class CustomerProfileRepository
{
    private string $storageBasePath;

    public function __construct(string $storageBasePath)
    {
        $this->storageBasePath = $storageBasePath;
    }

    private function getProfilesDir(): string
    {
        $dir = $this->storageBasePath . '/profiles';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    public function generateVisitorId(): string
    {
        return 'vis_' . strtoupper(substr(md5(uniqid()), 0, 8));
    }

    /**
     * @param string $visitorId The visitor/device ID
     * @param array $profileData ['name' => '...', 'mobile' => '...', 'dob' => '...', 'language' => '...']
     * @param string $linkedReportId The report they are unlocking
     */
    public function upsertProfile(string $visitorId, array $profileData, string $linkedReportId): void
    {
        $file = $this->getProfilesDir() . '/' . $visitorId . '.json';
        
        $profile = [];
        if (file_exists($file)) {
            $profile = json_decode(file_get_contents($file), true) ?: [];
        }

        $profile['visitor_id'] = $visitorId;
        $profile['identity'] = array_merge($profile['identity'] ?? [], $profileData);
        $profile['last_updated'] = date('c');
        
        if (!isset($profile['analyses'])) {
            $profile['analyses'] = [];
        }
        
        if (!in_array($linkedReportId, $profile['analyses'])) {
            $profile['analyses'][] = $linkedReportId;
        }

        file_put_contents($file, json_encode($profile, JSON_PRETTY_PRINT));

        // Update the report's state.json
        $reportStateFile = $this->storageBasePath . '/analysis/' . $linkedReportId . '/state.json';
        if (file_exists($reportStateFile)) {
            $state = json_decode(file_get_contents($reportStateFile), true) ?: [];
            $state['state'] = 'premium';
            $state['profile_completed'] = true;
            $state['premium_unlocked'] = true;
            $state['updated_at'] = date('c');
            file_put_contents($reportStateFile, json_encode($state, JSON_PRETTY_PRINT));
        }
    }

    public function getProfile(string $visitorId): ?array
    {
        $file = $this->getProfilesDir() . '/' . $visitorId . '.json';
        if (file_exists($file)) {
            return json_decode(file_get_contents($file), true);
        }
        return null;
    }
}
