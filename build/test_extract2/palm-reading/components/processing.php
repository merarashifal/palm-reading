<?php
if (!defined('ABSPATH')) exit;
?>
<div id="ppb-processing-view" class="ppb-flex-col ppb-flex-center ppb-hidden" style="max-width: 500px; margin: 0 auto; text-align: center;">
    <h2 class="ppb-mb-lg">Analyzing Your Palm</h2>
    
    <div class="ppb-skeleton" style="width: 120px; height: 120px; border-radius: 50%; margin: 0 auto var(--space-xl) auto;"></div>
    
    <div id="ppb-progress-list" class="ppb-flex-col ppb-gap-sm" style="text-align: left; width: 100%; max-width: 300px; margin: 0 auto;">
        <!-- Filled dynamically by JS -->
        <div class="ppb-progress-item text-muted" id="prog-1">⏳ Verifying image quality...</div>
        <div class="ppb-progress-item text-muted ppb-hidden" id="prog-2">⏳ Detecting palm landmarks...</div>
        <div class="ppb-progress-item text-muted ppb-hidden" id="prog-3">⏳ Measuring major lines...</div>
        <div class="ppb-progress-item text-muted ppb-hidden" id="prog-4">⏳ Identifying secondary lines...</div>
        <div class="ppb-progress-item text-muted ppb-hidden" id="prog-5">⏳ Looking for rare signs...</div>
        <div class="ppb-progress-item text-muted ppb-hidden" id="prog-6">⏳ Building your Personal Palm Blueprint...</div>
    </div>
</div>
