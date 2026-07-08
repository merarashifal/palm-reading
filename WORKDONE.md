# AI Knowledge Platform - Work Log

This document serves as the living biography of the AI Knowledge Platform project. It details all completed work, major architectural shifts, files created, and issues overcome. It must be updated immediately upon the completion of any new progress.

---

## 1. Architectural Foundation & Constitution
**Status: ✅ Complete**

The project originally began as a simple "AI Palm Reading Application" where an image was sent to an LLM, and the LLM generated a text report. We realized this approach was non-deterministic, hallucinatory, and incapable of supporting an enterprise-grade product. 

We executed a massive architectural shift to build a **domain-agnostic Knowledge Engine** where AI only *observes*, and compiled Knowledge Packs perform the *reasoning*.

**Files Created/Edited:**
- `docs/VISION.md`: The definitive project constitution, product manifesto, and long-term vision.
- `docs/ARCHITECTURE.md`: High-level system design.
- `docs/ProviderCertification.md`: Strict rules for integrating new AI models.
- `docs/PalmAnnotationSchema.md`: The immutable contract for computer vision extraction.
- `docs/ObservationToken.md`: Frozen vocabulary for visual evidence.
- `docs/PublicAPI.md`, `docs/CustomerJourney.md`, `docs/CommerceSpecification.md`

**Issues Faced & Resolved:**
- **Issue**: Tightly coupled domain logic. 
- **Resolution**: Decoupled the entire platform. The engine no longer knows what "Palmistry" is. All logic lives in JSON Knowledge Packs.

---

## 2. Sprint A1: The Gemini Provider Pipeline (Vertical Slice Base)
**Status: ✅ Complete**

We implemented the first real AI Provider (Google Gemini 2.5 Flash) using strict adherence to the Palm Annotation Schema.

**Work Done:**
- Built the core HTTP client with strict timeout controls (10s connect, 45s execution) and exponential backoff retries specifically for 429, 500, and 503 errors.
- Built a strict JSON parser that fails fast if the schema is broken (no guessing or auto-fixing).
- Designed the `ExtractedFeature` DTO to handle completely generic observations (visual confidence, geometry confidence, bounding boxes, evidence tokens).
- Built the `GeminiProviderPipeline` to map these raw extractions into the engine's `NormalizedFeatureCollection`.
- Added deep telemetry tracking: token usage, estimated cost in INR, latency, and image hashing saved to `storage/provider/`.

**Files Created/Edited:**
- `engine/src/AI/Providers/Gemini/GeminiClient.php`
- `engine/src/AI/Providers/Gemini/GeminiResponse.php`
- `engine/src/AI/Providers/Gemini/GeminiFeatureExtractor.php`
- `engine/src/AI/Providers/Gemini/GeminiProviderPipeline.php`
- `engine/src/AI/Providers/DTO/ExtractedFeature.php`
- `engine/src/AI/Providers/DTO/ExtractedFeatureCollection.php`
- `engine/src/Config/EnvLoader.php`
- `engine/.env.example`

**Issues Faced & Resolved:**
- **Issue**: Gemini outputting markdown formatting (```json) breaking the native `json_decode`.
- **Resolution**: Implemented specific markdown stripping in `GeminiResponse` before validation.
- **Issue**: Gemini hallucinating predictions (e.g., "This person will be wealthy").
- **Resolution**: Rewrote the prompt (`v1.txt`) to enforce a strict GIS-style geometry schema. Gemini is now treated purely as a bounding-box and polyline detector, forced to justify its detections using predefined `ObservationTokens`.

---

## 3. CLI Tooling & Benchmarking Scaffold
**Status: ✅ Complete**

To iterate quickly without needing a heavy WordPress stack, we built robust CLI tooling to test the engine locally.

**Work Done:**
- Created rapid prompt iteration tools.
- Built a benchmarking scaffold to compare Gemini outputs against `.expected.json` ground truth files.

**Files Created/Edited:**
- `engine/bin/gemini`: End-to-end execution of a single image.
- `engine/bin/prompt-test`: Rapid iteration tester for prompt engineering.
- `engine/bin/benchmark`: Scans datasets, calculates accuracy/cost metrics, and outputs HTML reports.

**Issues Faced & Resolved:**
- **Issue**: Windows PowerShell syntax issues (`&&` vs `;`) and missing local PHP PATH configurations preventing automated tests.
- **Resolution**: Shifted focus to building the tools for the user to execute in their local environment, relying on manual user testing for validation.

---

## 4. Sprint A2 & A3: Initial Knowledge Pack & HTML Renderer
**Status: ✅ Complete**

Began building the first pieces of the commercial product layer.

**Work Done:**
- Wrote the first curated Knowledge Pack for Palmistry, moving away from code-based `if/else` rules to pure JSON evaluation.
- Built a visually polished HTML Renderer to output the "Free Report" containing scores, star ratings, and CTA cards.

**Files Created/Edited:**
- `engine/knowledge/palmistry_v1.json`: The first 4 curated inference rules.
- `engine/src/Renderer/HtmlRenderer.php`: Generates the UI.
- `engine/bin/render`: A CLI tool to mock Inference results and output `demo_report.html`.

**Issues Faced & Resolved:**
- **Issue**: The initial HTML Renderer hardcoded domain concepts like "Career" and "Relationships", violating the engine's generic architecture.
- **Resolution**: (Currently drafting Sprint A4.1 to refactor this). A `PresentationEngine` and `ReportModel` are being introduced to ensure the Renderer simply loops through generic data provided by the engine, keeping the Renderer 100% blind to the domain.

---

## 5. Sprint A4.1: Real Inference & Presentation Layer (The 80/20 Slice)
**Status: ✅ Complete**

Shifted from pure architecture to product-driven execution ("80/20 Rule"), prioritizing time-to-value and customer-facing deliverables over heavy abstractions.

**Work Done:**
- Discarded mocked inference in favor of a real, lightweight `InferenceRuntime`.
- Implemented `RuleMatcher` and `ConditionEvaluator` to parse conditions defined in JSON Knowledge Packs.
- Implemented `InsightScorer` to compute dynamic UI metrics (e.g., Overall Strength) and `RecommendationBuilder` to generate commercial up-sell data.
- Built the `PresentationEngine` to consume the raw `InferenceResult` and dynamically output a domain-agnostic `ReportModel`.
- Rewrote `HtmlRenderer.php` to render beautiful UI cards, star ratings, and locked premium insights based solely on the `ReportModel` data, completely decoupling HTML from palmistry logic.
- Built `bin/demo` to orchestrate this end-to-end flow and isolate output artifacts in `storage/demo/run-UUID/`.
- Consolidated `palmistry_v1.json` to 10 high-quality, high-impact rules to power the initial commercial slice.

**Files Created/Edited:**
- `engine/src/Inference/DTO/Insight.php`
- `engine/src/Inference/DTO/Recommendation.php`
- `engine/src/Inference/DTO/InferenceResult.php`
- `engine/src/Inference/Runtime/ConditionEvaluator.php`
- `engine/src/Inference/Runtime/InferenceRuntime.php`
- `engine/src/Inference/Runtime/InsightScorer.php`
- `engine/src/Inference/Runtime/RecommendationBuilder.php`
- `engine/src/Inference/Runtime/RuleMatcher.php`
- `engine/src/Presentation/DTO/ReportModel.php`
- `engine/src/Presentation/PresentationEngine.php`
- `engine/src/Renderer/HtmlRenderer.php`
- `engine/bin/demo`

**Issues Faced & Resolved:**
- **Issue**: The original plan included a massive, over-engineered Stage/Pipeline framework and heavy telemetry.
- **Resolution**: Pivoted based on CTO directives to strip out pure infrastructure code. Focused entirely on completing the vertical slice from Gemini data to HTML output to guarantee a demonstrable product immediately.

---

## 6. Sprint A5: WordPress Integration (The Customer Flow)
**Status: ✅ Complete**

Built the first commercial client of the Engine, establishing a strict boundary between the core platform (engine) and the frontend delivery (WordPress plugin).

**Work Done:**
- Created a brand new, isolated Git repository at `d:\Antarman\code\palm-reading-wordpress`.
- Built the `EngineFacade::analyze()` entry point in the Engine repository to serve external clients securely.
- Built a streamlined WordPress plugin with exactly one shortcode (`[palm_reader]`).
- Implemented `EngineAdapter` in the plugin to load the local Engine vendor files and fetch the `EngineResult` directly (avoiding REST API overhead for v1).
- Built the frontend AJAX upload flow, storing images directly in `engine/storage/uploads/` instead of the WordPress media library.
- Built the real-time JS processing screen with psychological anticipation steps.
- The WordPress plugin now blindly renders the HTML generated by the Engine, achieving 100% decoupling from domain logic.

**Files Created/Edited:**
- *Engine Repo*: `engine/src/Facade/EngineFacade.php`, `engine/src/Facade/DTO/EngineResult.php`
- *Plugin Repo*: `palm-reading.php`, `includes/class-engine-adapter.php`, `includes/class-premium.php`, `includes/class-upload.php`, `includes/class-shortcodes.php`, `templates/upload.php`, `templates/processing.php`, `assets/css/style.css`, `assets/js/main.js`

---

## Current Status: Pending Sprint A6 (Premium Conversion Flow)
The core customer journey is complete. We now focus on monetization:
1. Integrating Razorpay.
2. Building the "Unlock Complete Report" premium flow.
3. Enabling real Gemini integration to process uploaded images in production.
