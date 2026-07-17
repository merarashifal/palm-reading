document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('ppb-upload-form');
    const uploadView = document.getElementById('ppb-upload-view');
    const processingView = document.getElementById('ppb-processing-view');
    const dropzone = document.getElementById('ppb-dropzone');
    const fileInput = document.getElementById('ppb_file');
    
    // Drag & Drop Handlers
    if (dropzone) {
        dropzone.addEventListener('click', () => fileInput.click());
        dropzone.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('drag-active'); });
        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('drag-active'));
        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('drag-active');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
            }
        });
    }

    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!fileInput.files.length) {
                alert('Please select an image first.');
                return;
            }

            // Show Processing View
            uploadView.classList.add('ppb-hidden');
            processingView.classList.remove('ppb-hidden');
            
            startProgressSequence();

            // AJAX Upload Simulation (Connecting to EngineFacade)
            const formData = new FormData(uploadForm);
            formData.append('action', 'ppb_upload');
            formData.append('_ajax_nonce', ppbConfig.nonce);

            fetch(ppbConfig.ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    setTimeout(() => {
                        window.location.href = data.data.report_url || '/report/PPB-DEMO';
                    }, 5000); 
                } else {
                    showErrorState('Analysis Failed', data.data.message || 'Something unexpected happened. Your upload is safe. Please try again.');
                }
            })
            .catch(err => {
                showErrorState('Upload Failed', 'We couldn\'t upload your image. Please check your connection and try another image.');
            });
        });
    }

    function showErrorState(title, message) {
        uploadView.classList.add('ppb-hidden');
        processingView.classList.add('ppb-hidden');
        
        // Remove existing error if any
        const existingError = document.getElementById('ppb-error-state');
        if (existingError) existingError.remove();

        const errorHtml = `
            <div id="ppb-error-state" class="ppb-status-card ppb-card ppb-text-center ppb-slide-up" style="border-top: 4px solid var(--danger); max-width: 500px; margin: 0 auto;">
                <h3 class="ppb-mb-sm" style="color: var(--danger);">${title}</h3>
                <p class="text-md text-muted ppb-mb-lg">${message}</p>
                <button class="ppb-btn ppb-btn-secondary" onclick="window.location.reload()">Try Again</button>
            </div>
        `;
        
        uploadView.insertAdjacentHTML('afterend', errorHtml);
    }

    // Modal & Unlock Flow (Day 2)
    const triggerUnlockBtn = document.getElementById('ppb-trigger-unlock');
    const profileModal = document.getElementById('ppb-profile-modal');
    const modalClose = document.getElementById('ppb-modal-close');
    const profileForm = document.getElementById('ppb-profile-form');

    if (triggerUnlockBtn && profileModal) {
        triggerUnlockBtn.addEventListener('click', () => {
            profileModal.classList.remove('ppb-hidden');
        });

        modalClose.addEventListener('click', () => {
            profileModal.classList.add('ppb-hidden');
        });

        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Extract Report ID from URL (/report/PPB-XXXXXX)
            const pathParts = window.location.pathname.split('/');
            const reportId = pathParts[pathParts.length - 1] || pathParts[pathParts.length - 2];
            
            const formData = new FormData(profileForm);
            formData.append('action', 'ppb_unlock');
            formData.append('_ajax_nonce', ppbConfig.nonce);
            formData.append('report_id', reportId);

            const submitBtn = profileForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerText;
            submitBtn.innerText = 'Unlocking...';
            submitBtn.disabled = true;

            fetch(ppbConfig.ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    profileModal.classList.add('ppb-hidden');
                    // Replace the entire report container with the premium HTML
                    const reportContainer = document.querySelector('.ppb-container');
                    if (reportContainer && data.data.premium_html) {
                        reportContainer.innerHTML = data.data.premium_html;
                    }
                } else {
                    alert('Error: ' + data.data.message);
                    submitBtn.innerText = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(err => {
                alert('Connection error occurred.');
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    function startProgressSequence() {
        const items = document.querySelectorAll('.ppb-progress-item');
        let i = 0;
        const interval = setInterval(() => {
            if (i < items.length) {
                if (i > 0) items[i-1].innerHTML = items[i-1].innerHTML.replace('⏳', '✓').replace('text-muted', 'text-success');
                items[i].classList.remove('ppb-hidden');
                items[i].classList.add('ppb-slide-up');
                i++;
            } else {
                clearInterval(interval);
            }
        }, 1500);
    }

    function resetUploadView() {
        uploadView.classList.remove('ppb-hidden');
        processingView.classList.add('ppb-hidden');
    }
});
