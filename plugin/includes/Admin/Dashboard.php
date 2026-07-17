<?php

namespace AIAnalysisEngine\Admin;

class Dashboard
{
    public static function init()
    {
        add_action('admin_menu', [self::class, 'addDashboardMenu']);
    }

    public static function addDashboardMenu()
    {
        add_submenu_page(
            'ppb-settings',
            'Overview',
            'Overview',
            'manage_options',
            'ppb-dashboard',
            [self::class, 'renderDashboard']
        );
    }

    public static function renderDashboard()
    {
        // Mock data for Dashboard v2 based on user request
        $funnelData = [
            'Landing' => '100%',
            'Upload' => '72%',
            'Analysis Complete' => '69%',
            'Unlock' => '44%',
            'Profile Complete' => '31%',
            'Feedback' => '18%',
            'PDF' => '12%',
            'Share' => '6%'
        ];

        ?>
        <div class="wrap">
            <h1>Personal Palm Blueprint - Founder Dashboard</h1>
            
            <div style="display: flex; gap: 20px; margin-top: 20px;">
                <div style="flex: 2;">
                    <h2>Key Metrics (Today)</h2>
                    <table class="widefat">
                        <tr><td>Visitors</td><td><strong>1,240</strong></td></tr>
                        <tr><td>Uploads</td><td><strong>892</strong></td></tr>
                        <tr><td>Success Rate</td><td><strong>96%</strong></td></tr>
                        <tr><td>Unlock %</td><td><strong>64%</strong></td></tr>
                        <tr><td>Average Rating</td><td><strong>4.8</strong></td></tr>
                        <tr><td>Most Read Insight</td><td><strong>Career Potential</strong></td></tr>
                    </table>
                </div>

                <div style="flex: 1;">
                    <h2>Funnel Conversion</h2>
                    <table class="widefat striped">
                        <?php foreach ($funnelData as $step => $pct): ?>
                        <tr>
                            <td><?php echo esc_html($step); ?></td>
                            <td><strong><?php echo esc_html($pct); ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            
            <div style="margin-top: 40px;">
                <h2>Recent Feedback</h2>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th>Rating</th>
                            <th>Impressed Most</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>5★</td><td>Accuracy</td><td>"Spot on about my career changes."</td></tr>
                        <tr><td>4★</td><td>Design</td><td>"Beautiful interface, quick result."</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
}
