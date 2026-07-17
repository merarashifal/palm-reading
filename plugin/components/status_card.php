<?php
if (!defined('ABSPATH')) exit;

/**
 * Expected variables:
 * $statusType: 'success', 'warning', 'error', 'info'
 * $statusTitle: string
 * $statusMessage: string
 * $statusActionText: string (optional)
 * $statusActionUrl: string (optional)
 * $statusActionId: string (optional)
 */

$statusType = $statusType ?? 'info';
$statusTitle = $statusTitle ?? 'Status';
$statusMessage = $statusMessage ?? '';

$colors = [
    'success' => 'var(--success)',
    'warning' => 'var(--warning)',
    'error' => 'var(--danger)',
    'info' => 'var(--primary)'
];
$color = $colors[$statusType] ?? $colors['info'];
?>
<div class="ppb-status-card ppb-card ppb-text-center" style="border-top: 4px solid <?php echo esc_attr($color); ?>; max-width: 500px; margin: 0 auto;">
    <h3 class="ppb-mb-sm" style="color: <?php echo esc_attr($color); ?>;">
        <?php echo esc_html($statusTitle); ?>
    </h3>
    <p class="text-md text-muted ppb-mb-lg">
        <?php echo esc_html($statusMessage); ?>
    </p>
    
    <?php if (!empty($statusActionText)): ?>
        <?php if (!empty($statusActionUrl)): ?>
            <a href="<?php echo esc_url($statusActionUrl); ?>" class="ppb-btn ppb-btn-secondary">
                <?php echo esc_html($statusActionText); ?>
            </a>
        <?php elseif (!empty($statusActionId)): ?>
            <button id="<?php echo esc_attr($statusActionId); ?>" class="ppb-btn ppb-btn-secondary">
                <?php echo esc_html($statusActionText); ?>
            </button>
        <?php endif; ?>
    <?php endif; ?>
</div>
