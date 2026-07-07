# AI Knowledge Platform - Developer Guide

Welcome to the AI Knowledge Platform. This engine is a completely decoupled, deterministic, and explainable inference machine designed to process normalized AI features into deterministic insights via O(1) indexed knowledge packs.

## Core Concepts

The system architecture cleanly separates Knowledge Authoring, Validation, Compilation, Generation, and Inference. This guarantees that runtime AI logic is fully decoupled from the filesystem and database layers.

### The Pipeline Lifecycle

1. **Validation Engine**: Strictly enforces schema typing and editorial standards on the raw knowledge JSON objects.
2. **Compiler Pipeline**: Merges raw knowledge into tightly packed, O(1) indexable binary/JSON artifacts (`CompiledKnowledgePack`).
3. **Generator Pipeline**: Transpiles the `CompiledKnowledgePack` into deployment targets (e.g., SQL schema, pure JSON blocks, CSV).
4. **Inference Engine**: The runtime machine. It takes a raw AI feature payload and crosses it with the `CompiledKnowledgePack` to construct an explainable `InferenceResult`.
5. **AI Adapters (v0.9+)**: Connect external APIs (e.g., Gemini Vision) into normalized engine payloads.

---

## Extension Points & API Stability

To keep the platform robust, adhere strictly to these extension guidelines.

### Stable APIs (Do Not Modify Core)
- `InferencePipeline`, `ValidationPipeline`, `CompilerPipeline`
- O(1) `RuleIndex`
- Core Engine DTOs (`Candidate`, `Evidence`, `InferenceResult`)

### Supported Extension Points (Safe to Extend)
- **Validators**: Implement `PipelineStageInterface` in `Validation/Stages/`.
- **Compilers**: Implement `PipelineStageInterface` in `Compiler/Stages/`.
- **Inference Logic**: Create highly focused, single-responsibility stages inside `Inference/Stages/` (e.g., `CustomConflictResolver`). Register them inside `InferenceFactory`.
- **Renderers**: Subscribe to the final `InferenceResult` object. The Renderer must NEVER modify the result or access the `RuleIndex`.
- **AI Adapters**: Must implement `VisionAdapterInterface` and return purely normalized Feature objects. They must *never* return provider-specific arrays or perform confidence aggregation.

### Internal-Only Classes (Do Not Subclass)
- `PipelineResult`, `StageResult`
- `InferenceContext` properties (use immutable methods on DTOs instead).

---

## Coding Conventions

1. **DTO-First**: Never pass associative arrays through pipelines. Always wrap data in strictly typed DTOs (e.g., `CandidateCollection`).
2. **Immutable Transitions**: Any state change (e.g., changing a candidate's score) must be done via immutable cloned transitions (e.g., `$candidate->withScore()`).
3. **Reason Codes**: Every logic branch in the Inference Engine must log a valid `ReasonCode` inside a `CandidateHistoryEntry` to maintain 100% explainability.
4. **No Math in Resolvers**: Logic stages (like `ConflictResolver`) should only make structural decisions (e.g., comparing scores), they should never mathematically recalculate values.
5. **Renderer Boundary**: Renderers strictly read the `InferenceResult`. They have zero awareness of candidates or underlying evidence.

---

## Releasing a Knowledge Pack

The QA process is heavily automated.

1. Ensure your rules sit in `knowledge/`.
2. Run `php bin/qa`.
3. If all tests, invariant checks, and benchmarks pass, the system will output a certified `release/` directory containing `engine_health.json`, `certification.json`, and the deployed packs.
