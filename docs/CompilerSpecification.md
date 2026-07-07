# Knowledge Compiler Specification

## Compiler Pipeline Architecture
The Knowledge Compiler transforms a validated Knowledge Pack into a single, optimized JSON payload (`knowledge_pack.json`) ready for downstream generation. It relies on a pipeline of single-responsibility compiler modules.

## Compiler Lifecycle
The compilation process strictly follows this deterministic lifecycle:
```text
KnowledgeRegistry
      ↓
CompilerContext
      ↓
ManifestCompiler
      ↓
DictionaryCompiler
      ↓
RuleCompiler
      ↓
MetadataCompiler
      ↓
StatisticsCompiler
      ↓
CompiledKnowledgePack
```

## Module Responsibilities

### 1. ManifestCompiler
**Responsibility**: Compiles the `manifest.json`.
- Normalizes languages (unique, lowercase, sorted).
- Normalizes features (trim, lowercase, unique, sorted).
- Normalizes entry points (fixes slashes, unique, sorted).
- Populates `CompiledKnowledgePack->manifest`.
- Gathers manifest-related statistics (counts of languages, features, entry points).

### 2. DictionaryCompiler
**Responsibility**: Compiles all editorial, semantic, and general dictionaries.
- Populates `CompiledKnowledgePack->dictionaries`.

### 3. RuleCompiler
**Responsibility**: Compiles all analysis rules.
- Deduplicates, sorts, and normalizes rule logic.
- Verifies `rule_uid` uniqueness.
- Populates `CompiledKnowledgePack->rules`.

### 4. MetadataCompiler
**Responsibility**: Handles all build metadata.
- Calculates checksums for all individual compiled parts.
- Populates `CompiledKnowledgePack->build` with version data, timestamps, checksums, and execution steps.

### 5. StatisticsCompiler
**Responsibility**: Merges and finalizes all statistics.
- Populates `CompiledKnowledgePack->statistics` based on outputs from earlier compilers.

## Input/Output Contracts
- **Input**: Validated knowledge pack data from the `KnowledgeContext` and `KnowledgeRegistry` (no direct file I/O).
- **Output**: A fully populated `CompiledKnowledgePack` DTO, serialized by a final File Generator.

## DTO Definitions
- **CompilerContext**: Holds `KnowledgeContext`, `KnowledgeRegistry`, `CompiledKnowledgePack`, and `LoggerInterface`.
- **CompilerResult**: Standardizes module output (success, errors, warnings, statistics, execution time, memory usage).
- **CompilationReport**: Aggregates all `CompilerResult` objects and the final pack.
- **CompiledKnowledgePack**: Immutable data structure containing `manifest`, `metadata`, `statistics`, `dictionaries`, `rules`, and `build`.

## Error Handling
- Modules may throw `CompilerException` for unrecoverable errors.
- The `KnowledgeCompiler` orchestrator traps exceptions and halts compilation immediately (Fail-fast).
- Failing modules return `CompilerResult` with `success = false` and corresponding error messages.

## Performance Targets
- Individual compiler modules should target execution times under **5 ms**.
- Excessive execution time generates a warning rather than failing the build to accommodate variable testing environments.

## Compiler Guarantees
- Never modifies editorial content.
- Never validates knowledge.
- Never writes SQL.
- Never depends on WordPress.
- Never reads files directly (only via Registry).
- Produces deterministic output.
