# ADR 004: Rule Compiler Multi-Stage Orchestration

## Context
The `RuleCompiler` is the heaviest module in the engine, responsible for transforming tens of thousands of raw JSON rules into the `CompiledRuleCollection`. Writing this as a single monolithic function would severely violate SOLID principles and make it impossible to test or maintain.

## Decision
We designed the `RuleCompiler` as a lightweight **Orchestrator** that delegates all work to single-responsibility handlers:
1. **RuleLoader**: Fetches raw data from the Registry.
2. **RuleNormalizer**: Safely maps JSON keys to the `CompiledRule` DTO and applies defaults.
3. **RuleCollectionBuilder**: Wires the normalized objects into the collection.
4. **RuleOptimizer**: Extracts the rules, sorts them by priority, removes exact UID duplicates, and builds a brand new clean collection.

## Consequences
**Positive:**
- The orchestrator itself is less than 80 lines of code.
- Each handler can be independently unit tested and benchmarked.
- Changes to the JSON schema only affect the `RuleNormalizer`.
- Business logic for deduplication lives exclusively in the `RuleOptimizer`, keeping the DTOs and collections passive.

**Negative:**
- Increased class count. Developers must traverse multiple small files to see the entire rule transformation lifecycle.
