<?php
if (!defined('ABSPATH')) exit;
?>
<div id="ppb-upload-view" class="ppb-flex-col ppb-flex-center" style="max-width: 500px; margin: 0 auto;">
    <h2 class="ppb-mb-md">Let's Get Started</h2>
    <p class="text-muted ppb-mb-lg text-center">Upload a clear photo of your dominant palm (the one you write with).</p>

    <form id="ppb-upload-form" class="ppb-container-sm" style="width: 100%;">
        <div class="ppb-form-group">
            <label class="ppb-label" for="ppb_name">Your Name (optional)</label>
            <input type="text" id="ppb_name" name="ppb_name" class="ppb-input" placeholder="e.g. Tushar">
        </div>

        <div class="ppb-form-group">
            <label class="ppb-label" for="ppb_language">Preferred Language</label>
            <select id="ppb_language" name="ppb_language" class="ppb-select">
                <option value="en">English</option>
                <option value="hi">Hindi</option>
            </select>
        </div>

        <div class="ppb-form-group ppb-mb-lg">
            <label class="ppb-label">Upload Palm Image</label>
            <div id="ppb-dropzone" class="ppb-upload-area">
                <div class="ppb-upload-icon">📷</div>
                <p class="text-sm">Click to browse or drag image here</p>
                <p class="text-xs text-muted">Supports JPG, PNG (Max 5MB)</p>
            </div>
            <input type="file" id="ppb_file" name="ppb_file" class="ppb-file-input" accept="image/jpeg, image/png">
        </div>

        <button type="submit" id="ppb-submit-btn" class="ppb-btn ppb-btn-primary ppb-btn-full">Analyze My Palm</button>
    </form>
</div>
