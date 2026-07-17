<?php
if (!defined('ABSPATH')) exit;
if (!\AIAnalysisEngine\Config\Settings::isEnabled('feedback')) return;
?>
<div id="ppb-feedback-section" class="ppb-card ppb-mb-xl ppb-text-center">
    <h3 class="ppb-mb-md">How useful was your Personal Palm Blueprint?</h3>
    
    <form id="ppb-feedback-form">
        <div class="ppb-stars ppb-mb-md" style="font-size: 32px; color: var(--warning); cursor: pointer; user-select: none;">
            <span data-value="1">★</span>
            <span data-value="2">★</span>
            <span data-value="3">★</span>
            <span data-value="4">★</span>
            <span data-value="5">★</span>
        </div>
        <input type="hidden" name="rating" id="ppb-rating-val" value="5">

        <div id="ppb-feedback-step-2" class="ppb-hidden ppb-slide-up">
            <h4 class="ppb-mb-sm">What impressed you most?</h4>
            <div class="ppb-flex-center ppb-gap-sm ppb-mb-md" style="flex-wrap: wrap;">
                <label><input type="radio" name="impressed" value="personality"> Personality</label>
                <label><input type="radio" name="impressed" value="career"> Career</label>
                <label><input type="radio" name="impressed" value="relationships"> Relationships</label>
                <label><input type="radio" name="impressed" value="design"> Design</label>
                <label><input type="radio" name="impressed" value="accuracy"> Accuracy</label>
            </div>

            <div class="ppb-form-group">
                <label class="ppb-label">Anything we can improve?</label>
                <textarea name="comment" class="ppb-input" rows="3" placeholder="Tell us what you think..."></textarea>
            </div>

            <button type="submit" class="ppb-btn ppb-btn-primary">Submit Feedback</button>
        </div>
    </form>
    
    <div id="ppb-feedback-success" class="ppb-hidden">
        <p class="text-success text-md">Thank you for your feedback! It helps us improve.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.ppb-stars span');
    const ratingInput = document.getElementById('ppb-rating-val');
    const step2 = document.getElementById('ppb-feedback-step-2');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const val = parseInt(this.getAttribute('data-value'));
            ratingInput.value = val;
            stars.forEach(s => {
                s.style.color = parseInt(s.getAttribute('data-value')) <= val ? 'var(--warning)' : 'var(--neutral-300)';
            });
            step2.classList.remove('ppb-hidden');
        });
    });

    const form = document.getElementById('ppb-feedback-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('action', 'ppb_feedback');
            formData.append('_ajax_nonce', ppbConfig.nonce);
            // report_id handled dynamically or via hidden input in real implementation
            
            fetch(ppbConfig.ajaxUrl, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                form.classList.add('ppb-hidden');
                document.getElementById('ppb-feedback-success').classList.remove('ppb-hidden');
            });
        });
    }
});
</script>
