# ADR 005: Universal Pipelines

## Context
Across the AI Analysis Engine, we repeatedly perform sequential, multi-staged operations: Validating a pack, Compiling a pack, running Rule Inference, and eventually generating SQL/Files. Initially, the orchestration logic (looping, logging, error catching, fail-fast) was duplicated inside `KnowledgeCompiler` and `QAEngine`.

## Decision
We implemented a **Generic Pipeline Foundation** (`engine/src/Pipeline/`).
- Defines `Pipeline`, `PipelineStageInterface`, `PipelineContext`, and `PipelineResult`.
- All major engine sequences (Validation, Compilation, Inference) extend this generic pipeline.
- Pipelines enforce strict stage ordering via `priority`.
- Pipelines natively handle structured logging, execution timings, memory tracking, and global fail-fast mechanisms on exceptions.

## Consequences
**Positive:**
- Guarantees uniform orchestration across the entire ecosystem.
- `KnowledgeCompiler` is reduced to a trivial Factory that just registers stages and hits "execute".
- Adding a new compiler module (like `MetadataCompiler` or `StatisticsCompiler`) is now simply a one-line pipeline registration.
- Substantial reduction in duplicated boilerplate code.

**Negative:**
- Slight learning curve for new developers to understand how context states are passed between generic pipeline stages.
