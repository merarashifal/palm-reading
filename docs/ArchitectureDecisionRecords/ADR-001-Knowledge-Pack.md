# ADR 001: Knowledge Packs as Single Source of Truth

## Context
As the AI Analysis Engine expanded beyond Palm Reading into Face Reading, Numerology, and Vastu, hardcoding knowledge rules in PHP arrays or managing them haphazardly across fragmented JSON files became unscalable. We needed a deterministic, versionable, and strict format to define domain knowledge separately from application logic.

## Decision
We adopted the **Knowledge Pack** architecture. A Knowledge Pack is a strictly structured directory containing a `manifest.json`, dictionary files, and localized rule files. 
- The `manifest.json` acts as the immutable contract, defining exactly what the pack contains.
- Validation is strictly decoupled from runtime execution. A Knowledge Pack must pass intense Quality Assurance (QA) pipelines (Schema, Semantic, Reference, Editorial) before it can ever be compiled.
- No rule or dictionary is loaded unless explicitly declared in the manifest. There is no auto-discovery.

## Consequences
**Positive:**
- Domain experts can edit knowledge without touching PHP code.
- Knowledge changes are fully trackable via Git.
- Multi-language support (English/Hindi) is localized cleanly per pack.
- It prevents broken references from crashing the production application.

**Negative:**
- Imposes a strict schema requiring domain experts to adhere to specific JSON structures.
- Requires dedicated validation tooling to ensure pack integrity before compilation.
