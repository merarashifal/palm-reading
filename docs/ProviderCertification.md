# Provider Certification Standard

Every AI Provider adapter integrated into the AI Knowledge Platform must pass this rigorous certification checklist before being authorized for production. This guarantees that whether we use Gemini, OpenAI, Claude, or MediaPipe, the core Inference Engine remains perfectly decoupled, deterministic, and cost-aware.

## 1. Architectural Integrity
- [ ] **No Domain Knowledge**: The provider code contains absolutely zero hardcoded references to domain strings like "Palm" or "Face".
- [ ] **No Reasoning**: The provider performs zero conditional logic on the output (e.g., it never decides if a broken life line means "bad health").
- [ ] **Strict Normalization**: The provider outputs exactly the `NormalizedFeatureCollection` DTO.

## 2. Pipeline Compliance
- [ ] **Symmetrical Pipelines**: The provider uses the `ProviderExecutionPipeline` and `PreprocessingPipeline` separation.
- [ ] **Prompt Composition**: Prompts are not hardcoded. They are dynamically generated via `PromptComposer` into a `PromptDocument`.
- [ ] **Transport Decoupling**: HTTP calls do not bypass `HttpTransportInterface`.

## 3. Reliability & Testing
- [ ] **Golden Tests (Level 1)**: Raw provider responses deterministically map to the exact same `NormalizedFeatureCollection`.
- [ ] **Replay Tests (Test B)**: Production requests can be exactly replayed using `ReplayTransport` without making real API calls.
- [ ] **End-to-End Tests**: The provider can successfully route an image artifact through Inference to produce a valid `InferenceResult`.
- [ ] **Error Mapping**: Raw HTTP/API errors are explicitly mapped into the platform's standardized error taxonomy (`Retryable`, `Authentication`, `Quota`, `Fatal`).
- [ ] **Retry Logic**: Intermittent errors correctly adhere to the `RetryPolicy` backoff.

## 4. Observability & Diagnostics
- [ ] **Cost Reporting**: Token usage (prompt/completion/image) and latency metrics are populated into the `ProviderExecution` DTO.
- [ ] **Confidence Calibration**: Raw provider confidence scores are mapped through a `ConfidenceCalibration` DTO to standardized engine baselines.
- [ ] **Explainability (Timeline)**: Every pipeline stage (Preprocessing, Transport, Parsing, Normalization) records its exact millisecond duration to the session timeline.

## 5. Security & Governance
- [ ] **Validation Guardrails**: The output strictly passes the `FeatureValidatorPipeline` (Semantic, Duplicate, Coordinate bounds).
- [ ] **Credentials**: The adapter does not log or serialize API keys or sensitive authorization headers.

---

## 6. Platform Integration Gates
- [ ] **Replay Compatible**: The provider's raw transport payloads are perfectly serializable into an immutable `ReplayPackage`.
- [ ] **Observability Compatible**: The provider explicitly conforms to `StageTrace` and emits full `ProviderTrace` telemetry.
- [ ] **Golden Compatible**: The provider output can be cleanly swapped into a segmented Golden Test fixture (`Provider`, `Normalized`, `Inference`, `Trace`) without regression.

---

> **Certification Status:** Providers that pass this checklist are marked **Production Certified** and can be dynamically selected by `AnalysisDefinition` configurations.
