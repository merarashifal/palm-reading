# Knowledge Engine

This document defines the technical specification and architecture for the AI Analysis Platform's Knowledge Engine.

## 1. Architecture

The Knowledge Engine processes raw, human-curated knowledge packs into highly optimized, compiled structures and generated deliverables.

```
Knowledge
↓
Validation
↓
Compilation
↓
Generation
```

## 2. Validation Flow

Validation ensures that a Knowledge Pack adheres to all required standards before compilation.

```
JSON
↓
Schema (Validates strict JSON structure, UUIDs, UIDs, illegal chars)
↓
Reference (Checks existence of analysis, features, and sections)
↓
Editorial (Checks word count, tone, curiosity gaps, openings, quality score)
↓
Semantic (Validates thematic relevance, e.g., health terminology in health section)
↓
Statistics (Analyzes overall metrics of the pack)
```

## 3. Compiler Flow

The Compiler transforms the raw, multi-file structure into a unified, optimized internal representation.

```
Knowledge Pack (Manifest + Rules + Dictionaries)
↓
Compiler
↓
knowledge_pack.json (Internal unified representation)
```

## 4. Generator Flow

Generators consume the compiled `knowledge_pack.json` to produce specific output formats.

```
knowledge_pack.json
↓
SQL (For the AI Analysis database)
↓
CSV (For data import/export utilities)
↓
JSON (For API consumption)
↓
Markdown (For automated documentation)
```

## 5. Future Scalability

The Knowledge Engine is designed to support not only Palm Reading, but all future AI Analysis platforms:
- Palm
- Face
- Signature
- Numerology
- Vastu

## Core Components

- **KnowledgeRegistry**: Single source of truth. Discovers packs, loads manifests, dictionaries, and features. Caches everything for performance.
- **KnowledgeContext**: The state passed to all validators. Includes the Manifest, Configuration, Root Path, Environment, Compiler Version, Knowledge Version, and Language.
- **ValidationResult**: The standardized return object for all validators, featuring granular scoring, execution metrics, errors, and warnings for integration into the QA Dashboard.
