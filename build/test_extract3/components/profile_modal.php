<?php
if (!defined('ABSPATH')) exit;
?>
<!-- Profile Modal (Hidden by default, triggered by Day 2 JS) -->
<div id="ppb-profile-modal" class="ppb-modal-overlay ppb-hidden" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: var(--z-modal); display: flex; align-items: center; justify-content: center; padding: var(--space-md);">
    <div class="ppb-modal-content ppb-card ppb-slide-up" style="max-width: 500px; width: 100%; position: relative;">
        
        <button id="ppb-modal-close" style="position: absolute; top: var(--space-md); right: var(--space-md); background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-muted);">&times;</button>

        <h2 class="ppb-mb-md">Complete Your Personal Profile</h2>
        
        <div class="ppb-mb-lg text-muted text-md">
            <p class="ppb-mb-sm">You're just one step away from unlocking your complete <strong>Personal Palm Blueprint</strong>.</p>
            <p>We'll personalize your report and make it available anytime using your permanent report link.</p>
        </div>

        <form id="ppb-profile-form">
            <div class="ppb-form-group">
                <label class="ppb-label" for="ppb_mobile">Mobile Number</label>
                <input type="tel" id="ppb_mobile" name="ppb_mobile" class="ppb-input" placeholder="Enter your mobile number" required>
            </div>

            <div class="ppb-form-group ppb-mb-lg">
                <label class="ppb-label" for="ppb_dob">Date of Birth</label>
                <input type="date" id="ppb_dob" name="ppb_dob" class="ppb-input" required>
            </div>

            <div class="ppb-form-group ppb-mb-lg text-sm text-muted" style="display: flex; align-items: flex-start; gap: 8px;">
                <input type="checkbox" id="ppb_consent" name="ppb_consent" required style="margin-top: 4px;">
                <label for="ppb_consent">I agree to the Privacy Policy and Terms of Use. I consent to the processing of my uploaded palm image and personal data to generate my Personal Palm Blueprint.</label>
            </div>

            <button type="submit" class="ppb-btn ppb-btn-primary ppb-btn-full">Continue</button>
        </form>
    </div>
</div>
