# RuleCompiler Specification

## 1. Purpose
The `RuleCompiler` converts validated rule JSON into strictly typed, optimized runtime structures for lightning-fast AI inference.

## 2. Input
- **Source**: `KnowledgeRegistry` (fed strictly by the Manifest).
- **Format**: Validated JSON Rules.
- **Constraints**: No direct filesystem access, no SQL, no WordPress dependencies.

## 3. Output
- **Destination**: `CompiledKnowledgePack->rules`
- **Format**: A heavily indexed, strongly typed `CompiledRuleCollection` containing `CompiledRule` objects, nested `RuleGroup` structures, and owning the `RuleIndex` for O(1) lookups.
- **Serialization**: The final `knowledge_pack.json` will contain discrete blocks for `build`, `manifest`, `statistics`, `dictionaries`, and `rules` (which includes the indexes).

## 4. Compilation Stages
The compiler acts as an orchestrator. All logic is delegated to single-responsibility modules inside `engine/src/Knowledge/Compiler/Rule/`:
1. **RuleLoader**: Fetches raw rule arrays from the `KnowledgeRegistry`.
2. **RuleNormalizer**: Converts raw arrays into `CompiledRule` DTOs.
3. **RuleCollectionBuilder**: Aggregates `CompiledRule` DTOs into the `CompiledRuleCollection`.
4. **RuleIndexBuilder**: Builds the `RuleIndex` inside the collection for instantaneous lookups.
5. **RelationshipBuilder**: Compiles relationships into adjacency lists (Knowledge Graph).
6. **RuleOptimizer**: Pre-calculates rule groups, handles priority sorting, deduplication, and memory optimization.
7. **Freeze**: Locks runtime objects to make them immutable.

## 5. Rule Normalization
Raw JSON properties are sanitized, cast, and mapped precisely to a `CompiledRule` object. No business validation occurs here.

## 6. Compiled Objects Namespace
Runtime objects live in: `engine/src/Knowledge/Compiled/`
- `CompiledRule.php`
- `CompiledRuleCollection.php` (Implements `IteratorAggregate`, `Countable`)
- `RuleIndex.php`
- `CompiledDictionary.php` (future migration)
- `CompiledKnowledgePack.php`

## 7. CompiledRule DTO
```php
class CompiledRule
{
    public string $uid;
    public string $language;
    public string $analysis;
    public string $feature;
    public string $value;
    public string $section;
    public string $visibility;
    public array $translations;
    public float $confidence;
    public int $priority;
    public array $relationships = []; // Future: RelationshipCollection
}
```

## 8. CompiledRuleCollection
```php
class CompiledRuleCollection implements \IteratorAggregate, \Countable
{
    // Owns the index
    public RuleIndex $index;
    
    public function add(CompiledRule $rule): void;
    public function get(string $uid): ?CompiledRule;
    public function findByFeature(string $feature): array;
    public function findByVisibility(string $visibility): array;
    public function findBySection(string $section): array;
    public function findByUid(string $uid): array;
    public function all(): array;
    public function count(): int;
    public function getIterator(): \Traversable;
}
```

## 9. RuleIndex (Lookup Tables)
```php
class RuleIndex
{
    public function getByUid(string $uid): array;
    public function getByFeature(string $feature): array;
    public function getByFeatureValue(string $feature, string $value): array;
    public function getByAnalysisFeatureValue(string $analysis, string $feature, string $value): array;
    public function getBySection(string $section): array;
    public function getByVisibility(string $visibility): array;
}
```

## 10. Knowledge Graph (Relationships)
Rules will be compiled into adjacency lists to form a deterministic Knowledge Graph, allowing instant traversal (e.g., `Life Line` -> `Career` -> `Remedy`).

## 11. Localization
Translations are normalized into arrays. In the future, language maps will be compiled separately to save memory (Rule -> Translation IDs -> Language Map).

## 12. Visibility Handling
Tracked via `free`, `premium`, `internal`. The indexer creates strict boundaries for immediate isolation.

## 13. Group Hierarchy & Priority
Rules are sorted by priority and naturally grouped: `Analysis` -> `Feature` -> `Value` -> `Section` -> `Rules`.

## 14. Confidence
Confidence thresholds are preserved as immutable floats directly on the `CompiledRule`.

## 15. Runtime Principles
- Immutable objects.
- O(1) lookups.
- No filesystem access.
- No JSON parsing at runtime.
- No validation.
- No SQL.
- No network requests.
- No WordPress dependencies.

## 16. Compiler Guarantees
- Never edits or sanitizes editorial text.
- Never changes translations.
- Never validates knowledge.
- Never accesses the filesystem directly.
- Never generates SQL.
- Produces deterministic output.

## 17. Tests
- **Golden / Snapshot Tests**: Compares complete compiled collections against frozen expected arrays.
- **Integration Tests**: Verifies end-to-end pipeline execution with real registries.
- **Performance Tests**: Ensures compilation speed meets benchmarks.

## 18. Benchmarks
- **Compilation Speed**: 50,000 rules in `< 5 seconds`.
- **Runtime Lookup**: Rule retrieval in `< 1 ms`.
- **Memory Footprint**: `< 100 MB` usage for 50,000 rules during compilation and indexing.
