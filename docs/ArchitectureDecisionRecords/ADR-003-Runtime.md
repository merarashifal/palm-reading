# ADR 003: Runtime Object Model

## Context
At runtime (e.g., during a user's Palm Reading inference session), the Rule Engine must rapidly query rules based on features, sections, visibility, and priority. Standard PHP associative arrays require repeated looping and array filtering (`array_filter`), causing O(N) complexity for every lookup.

## Decision
We established a strict **Compiled Runtime Namespace** (`engine/src/Knowledge/Compiled/`).
- Raw arrays are permanently converted into immutable Data Transfer Objects (DTOs), such as `CompiledRule`.
- Collections (e.g., `CompiledRuleCollection`) encapsulate these DTOs.
- **O(1) Indexing:** The collection inherently maintains a `RuleIndex`. Whenever a rule is added, references to it are instantly injected into specialized lookup maps (e.g., `getByAnalysisFeatureValue`). 
- DTOs contain zero business logic. They are pure data containers.

## Consequences
**Positive:**
- Lightning-fast lookups (<1ms) regardless of the number of rules.
- Extremely low memory footprint because the indexes store object references, not deep copies.
- Strict typing prevents undefined array key errors in PHP 8.

**Negative:**
- The initial memory spike during compilation is slightly higher to build the index maps.
- Enforces strict discipline: developers cannot add logic methods (like `isPremium()`) directly to the DTO.
