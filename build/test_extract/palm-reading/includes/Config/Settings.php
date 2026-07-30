<?php

namespace AIAnalysisEngine\Config;

class Settings
{
    public static function init()
    {
        add_action('admin_menu', [self::class, 'addAdminMenu']);
        add_action('admin_init', [self::class, 'registerSettings']);
    }

    public static function addAdminMenu()
    {
        add_menu_page(
            'Personal Palm Blueprint',
            'Palm Blueprint',
            'manage_options',
            'ppb-settings',
            [self::class, 'renderSettingsPage'],
            'dashicons-palmtree',
            30
        );
    }

    public static function registerSettings()
    {
        register_setting('ppb_settings_group', 'ppb_feature_pdf');
        register_setting('ppb_settings_group', 'ppb_feature_feedback');
        register_setting('ppb_settings_group', 'ppb_feature_sharing');
        register_setting('ppb_settings_group', 'ppb_feature_analytics');
    }

    public static function isEnabled(string $feature): bool
    {
        return get_option('ppb_feature_' . $feature) === '1';
    }

    public static function renderSettingsPage()
    {
        ?>
        <div class="wrap">
            <h1>Personal Palm Blueprint Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('ppb_settings_group'); ?>
                <?php do_settings_sections('ppb_settings_group'); ?>
                
                <h2>Feature Flags</h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Enable PDF Downloads</th>
                        <td>
                            <input type="checkbox" name="ppb_feature_pdf" value="1" <?php checked(1, get_option('ppb_feature_pdf'), true); ?> />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Enable Feedback System</th>
                        <td>
                            <input type="checkbox" name="ppb_feature_feedback" value="1" <?php checked(1, get_option('ppb_feature_feedback'), true); ?> />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Enable Social Sharing</th>
                        <td>
                            <input type="checkbox" name="ppb_feature_sharing" value="1" <?php checked(1, get_option('ppb_feature_sharing'), true); ?> />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Enable Analytics</th>
                        <td>
                            <input type="checkbox" name="ppb_feature_analytics" value="1" <?php checked(1, get_option('ppb_feature_analytics'), true); ?> />
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
