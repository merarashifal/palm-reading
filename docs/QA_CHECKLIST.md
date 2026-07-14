# Quality Assurance (QA) Checklist

This checklist is mandatory before every release (e.g. Release Candidates and stable versions) to verify that the end-to-end processing pipeline handles all inputs safely and reliably.

## End-to-End Tests

### 1. File Upload Types
- [ ] Upload JPG/JPEG -> Pass
- [ ] Upload PNG -> Pass
- [ ] Reject GIF -> Failure (Graceful)
- [ ] Reject PDF -> Failure (Graceful)
- [ ] Reject tiny image -> Failure (Graceful)
- [ ] Reject corrupted image -> Failure (Graceful)
- [ ] Very large image -> Success (Auto-resized)

### 2. Image Content Quality
- [ ] Dark image -> Failure (Please upload a brighter image)
- [ ] Blurred image -> Failure (Image too blurry)
- [ ] Good image -> Success (Report generated)

### 3. API Resilience
- [ ] Gemini timeout -> Friendly retry/graceful failure
- [ ] Quota exceeded -> Friendly message

### 4. UI/UX Output Verification
- [ ] Good report is fully visible
- [ ] Premium CTA is clearly visible
- [ ] HTML is responsive on desktop
- [ ] Mobile responsive layout works smoothly
- [ ] No PHP warnings on screen
- [ ] No Fatal errors in logs
- [ ] No console errors in the browser developer tools

*Note: Document all test results with Run IDs (e.g., `run_20260714_xxx`) during the validation process.*
