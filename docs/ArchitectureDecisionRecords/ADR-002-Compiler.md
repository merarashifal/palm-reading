# ADR 002: Compiler Architecture

## Context
Once Knowledge Packs are validated, the system needs to load thousands of JSON rules into the application. Parsing JSON dynamically at runtime on every user request in WordPress would destroy performance, particularly when complex graph lookups or translations are involved.

## Decision
We implemented a **Single-Responsibility, In-Memory Compiler Pipeline**.
- The compiler transforms validated JSON files into highly optimized, strongly-typed PHP objects (the Runtime layer).
- The compiler *never* validates. It assumes the pack has already passed the QA Validation Engine.
- The compiler *never* reads the filesystem directly. It strictly uses the `KnowledgeRegistry` to fetch data.
- The compiler *never* interacts with SQL, WordPress APIs, or external network requests.
- The compilation happens entirely in-memory and executes via deterministic stages.

## Consequences
**Positive:**
- Compilation is incredibly fast (processing 50,000 rules in <5 seconds).
- Complete separation of concerns: JSON storage vs PHP runtime memory.
- The engine is fully portable. It does not depend on WordPress or any specific database.

**Negative:**
- Adds an intermediate build step. When a Knowledge Pack changes, the compiler must be run to generate the updated runtime state or SQL seed files.
