# AI Knowledge Platform - Architecture Overview

This document serves as the central architectural map for the AI Knowledge Platform. As of Version 1.0, the core engine architecture is considered **stable, deterministic, and immutable**. 

Before contributing to this repository or building integrations, it is crucial to understand the philosophy, data flow, and hard boundaries that define this system.

---

## 1. Design Philosophy

> Intelligence comes from Knowledge Packs.
> 
> AI Providers observe.
> 
> The Engine reasons.
> 
> Renderers communicate.
> 
> Clients consume.

## 2. What is this platform?

The AI Knowledge Platform is a deterministic execution operating system designed to decouple AI inference from domain knowledge. It ingests raw media (like images) or data, normalizes it using a provider-agnostic AI layer, and evaluates it against compiled, expert-authored Knowledge Packs to yield highly explainable, consistent, and structured analysis reports.

It is **NOT** a consumer application (like a WordPress plugin or mobile app). Consumer applications are clients that consume this platform.

## 3. System Context Diagram

```text
                    Clients

       WordPress
       REST API
       Android
       iOS
       CLI

              │
              ▼

       AI Knowledge Platform

    Validation
    Compiler
    Generator
    AI Adapters
    Inference
    Observability

              │
              ▼

      Knowledge Packs
```

## 4. Architectural Principles

Every contribution must preserve these principles:

1. **Deterministic**: The same inputs and knowledge pack must always produce the same output.
2. **Explainable**: Every inference must be traceable to evidence and rules.
3. **Immutable Runtime**: DTO transitions are immutable.
4. **Provider Agnostic**: No provider-specific assumptions outside AI/Providers.
5. **Knowledge Driven**: Business knowledge lives exclusively in Knowledge Packs.
6. **Renderer Independent**: Inference never generates HTML, Markdown, PDF, or UI.
7. **Observable**: Every execution can be replayed.
8. **Testable**: Every engine must support isolated testing.
9. **Replaceable**: Every subsystem must be replaceable without affecting others.
10. **Single Responsibility**: Every pipeline stage performs exactly one task.

## 5. The 6-Engine Architecture

The platform is composed of 6 distinct but interoperating engines:

1. **Validation Engine**: Enforces strict semantic, structural, and editorial correctness on raw knowledge source files.
2. **Compiler Engine**: Transforms validated human-readable rules (JSON/Markdown) into highly optimized binary indices for runtime execution.
3. **Generator Engine**: Outputs the compiled Knowledge Packs (SQL, JSON, CSV) ready for ingestion by the runtime or external systems.
4. **AI Adapter Engine (Provider Framework)**: A certified abstraction layer that connects to AI Models (Gemini, OpenAI, MediaPipe), stripping them of domain context and enforcing a single `NormalizedFeatureCollection` output.
5. **Inference Engine**: The runtime core. It takes normalized features, executes them against the compiled Knowledge Pack, handles conflicts, scores confidence, and emits the final report.
6. **Observability & Certification**: A platform-wide telemetry and replay tracing system that ensures every execution is perfectly debuggable and every provider perfectly compliant.

## 6. Data Flow

1. **Input**: A Client requests an analysis (e.g., via HTTP) providing raw data (an image).
2. **Prompt Generation**: The system retrieves the `AnalysisDefinition` and dynamically builds a `PromptDocument`.
3. **Provider Execution**: A certified Provider (e.g., Gemini) executes the prompt.
4. **Normalization**: The provider's raw output is forced into a deterministic `NormalizedFeatureCollection`.
5. **Inference**: The `InferencePipeline` evaluates these features against the loaded Knowledge Pack.
6. **Output**: An `InferenceResult` is returned to the client, accompanied by an `ExecutionTrace` capturing the entire lifecycle.

## 7. Extension Points (What you CAN build)

The architecture is designed to be extended, not modified.
- **Providers**: Implement the Provider interface to plug in new AI models. All providers must pass the Provider Certification.
- **Knowledge Packs**: Author new domain rules, dictionaries, and definitions (e.g., Face Reading, Astrology).
- **Consumer Applications**: Build REST APIs, WordPress plugins, Android apps, or iOS apps that interface with the engine.
- **Renderers**: Build custom logic to transform the `InferenceResult` into PDFs, UI elements, or voice generation.

## 8. Immutability & Public Contracts

To protect the deterministic guarantee, the following boundaries are frozen forever. They represent the platform's semantic-versioned public APIs:
- `NormalizedFeature` DTO
- `InferenceResult` DTO
- `ExecutionTrace` DTO
- **Knowledge Pack format**
- **Provider Contract**
- **Pipeline Contract**

**Domain Seepage**: Never add hardcoded domain logic (e.g., "palm", "health line") into the Inference Engine, Validation Engine, or Provider code. Domain knowledge lives *exclusively* in the Knowledge Packs.

## 9. Replay & Observability

Every execution can be saved as an immutable `ReplayPackage`. This package contains the exact input, the exact provider output, the normalized features, the inference result, and detailed telemetry (time, memory, matched rules). 

This means that if a bug occurs in production, it can be debugged entirely locally without ever calling the external AI provider again.

## 10. Compatibility Matrix

```text
Engine 1.0 supports:

- Knowledge Pack 1.x
- Provider Contract 1.x
- Replay Package 1.x
- Inference Result 1.x
```

## 11. What the platform does NOT do

The platform does not:
- store users
- manage authentication
- perform payments
- render HTML
- generate PDFs
- perform business workflows
- contain domain knowledge in code
- directly call WordPress
- directly call databases during inference

## 12. Non-Goals

The engine is not:
- an LLM
- a chatbot
- a prompt framework
- a workflow engine
- a CMS
- a frontend framework
- a WordPress plugin
- an analytics platform
