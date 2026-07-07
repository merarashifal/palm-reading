# AI Analysis Engine Roadmap

This is the central source of truth for the development milestones of the AI Analysis Engine and MeraRashifal Platform.

## Milestone Status

| Version | Description | Status |
| :--- | :--- | :---: |
| **v0.5** | Architecture Frozen (Foundation, Validator, Orchestration, Pipeline) | ✅ |
| **v0.6** | Compiler Complete (Manifest, Dictionary, Rule, Metadata, Statistics) | ✅ |
| **v0.7** | Knowledge Pack Generator (SQL, JSON, CSV) | ✅ |
| **v0.8** | Inference Engine Complete (Pure Inference & Reasoning) | ✅ |
| **v0.9** | AI Adapters (Integration) | 🔄 |
| **v1.0** | WordPress Integration (Upload, Payment, PDF, History) | ⏳ |

---

## Phase 1: Engine Foundation & QA (Completed)
- ✅ Repository Structure
- ✅ Knowledge Pack Definition
- ✅ Validation Engine (Schema, Semantic, Editorial)

## Phase 2: Knowledge Compiler (v0.6)
- ✅ Compiler Foundation
- ✅ Universal Pipeline Framework
- ✅ Manifest & Dictionary Compilers
- ✅ Rule Compiler Orchestrator
- ✅ Metadata Compiler
- ✅ Statistics Compiler

## Phase 2H: Knowledge Pack Generator (v0.7)
- ✅ SQL Generator
- ✅ JSON Generator
- ✅ CSV Generator
- ✅ Diagnostics Output
- ✅ **Engine CLI**: Fully decouple the engine with `engine compile`, `engine validate`, `engine generate`, `engine benchmark`, `engine doctor`.

## Phase 3: Runtime Inference Engine (v0.8)
- ✅ Load Compiled Knowledge
- ✅ Build Evidence from Features
- ✅ Execute O(1) Candidate Matching
- ✅ Resolve Conflicts & Priorities
- ✅ Traverse Graph Relationships
- ✅ Compute Confidence Scoring
- ✅ Assemble `InferenceResult` DTO
- ✅ Stabilization, Property Tests, Benchmarking, and Golden QA

## Phase 4: v0.9 AI Adapters (Integration)
*Sprint 4A: AI Adapter Contracts* ⏳
- Define `VisionAdapterInterface` and `NormalizedFeatureMapper`
- Establish normalized output payload format

*Sprint 4B: Provider Implementation* ⏳
- Build `GeminiAdapter` for image to feature extraction
- Ensure no reasoning or confidence aggregation occurs in adapter

*Sprint 4C: Confidence Calibration* ⏳
- Map AI Provider confidence to Engine AI Confidence baseline

*Sprint 4D: End-to-End Integration* ⏳
- Run E2E tests: Image -> AI Adapter -> Inference Engine -> InferenceResult

## Phase 5: WordPress Integration (v1.0)
- ⏳ Handle User Uploads
- ⏳ Call Vision Module
- ⏳ Pass to Rule Engine
- ⏳ Display Frontend Results
- ⏳ Razorpay Payment Gateway
- ⏳ PDF Generation & Download
- ⏳ Order History & Sessions

---

## Architecture Guarantees
1. **Single Source of Truth**: Knowledge Packs are the definitive source. No DB-driven rules.
2. **Zero Auto-Discovery**: Manifests explicitly map every required file.
3. **No Direct Filesystem Access**: The Compiler relies solely on the `KnowledgeRegistry`.
4. **Platform Independence**: Compilers do not rely on PHP environment details, WordPress, or SQL.
5. **Idempotency**: All compiler stages are safely repeatable without data duplication.
