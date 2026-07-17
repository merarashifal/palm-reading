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
                    // Redirect to the new report permanent URL
                    setTimeout(() => {
                        window.location.href = data.data.report_url || '/report/PPB-DEMO';
                    }, 5000); // Give them time to see the progression
                } else {
                    alert('Error: ' + data.data.message);
                    resetUploadView();
                }
            })
            .catch(err => {
                alert('Server error occurred.');
                resetUploadView();
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
